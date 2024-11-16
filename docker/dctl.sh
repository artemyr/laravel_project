#!/bin/bash
set -e

# Переходим в директорию скрипта
cd "$(dirname "${BASH_SOURCE[0]}")"

# Установка цветов для терминала
set_terminal_colors() {
    local shell=$(basename "$SHELL")

    case $shell in
        bash)
            export COLOR_RESET='\e[0m'
            export COLOR_ERROR='\e[31m'
            export COLOR_SUCCESS='\e[32m'
            export COLOR_WARNING='\e[33m'
            export COLOR_INFO='\e[34m'
            ;;
        zsh)
            export COLOR_RESET='%{\e[0m%}'
            export COLOR_ERROR='%{\e[31m%}'
            export COLOR_SUCCESS='%{\e[32m%}'
            export COLOR_WARNING='%{\e[33m%}'
            export COLOR_INFO='%{\e[34m%}'
            ;;
        fish)
            export COLOR_RESET='\033[0m'
            export COLOR_ERROR='\033[0;31m'
            export COLOR_SUCCESS='\033[0;32m'
            export COLOR_WARNING='\033[0;33m'
            export COLOR_INFO='\033[0;34m'
            ;;
        *)
            echo "Неизвестная оболочка: $shell"
            return 1
            ;;
    esac
}

# Проверка зависимостей
check_dependencies() {
    local dependencies=("docker-compose" "sshpass" "rsync" "git")
    for dep in "${dependencies[@]}"; do
        if ! command -v $dep &> /dev/null; then
            echo -e "${COLOR_ERROR}Ошибка: $dep не установлен.${COLOR_RESET}"
            exit 1
        fi
    done
}

# Устанавливаем дефолтные значения для пользователя и группы
export DEFAULT_USER="1000"
export DEFAULT_GROUP="1000"

# Получаем ID пользователя и группы
export USER_ID=$(id -u)
export GROUP_ID=$(id -g)
export USER=$USER

# Если ID пользователя или группы равен 0, устанавливаем дефолтные значения
if [ "$USER_ID" == "0" ]; then
    USER_ID=$DEFAULT_USER
fi

if [ "$GROUP_ID" == "0" ]; then
    GROUP_ID=$DEFAULT_GROUP
fi

# Проверяем наличие .env файла и создаем его, если он отсутствует
test -e "./.env" || { cp .env.example .env; echo -e "${COLOR_SUCCESS}Создан файл .env из .env.example${COLOR_RESET}"; }

# Загружаем переменные окружения из .env файла
if [ -f "./.env" ]; then
    source ./.env
    echo -e "${COLOR_SUCCESS}Загружены переменные окружения из .env${COLOR_RESET}"
else
    echo -e "${COLOR_ERROR}Файл .env не найден${COLOR_RESET}"
    exit 1
fi

# Функция для выполнения команд в контейнере PHP
runInPhp() {
    local command=$@
    echo -e "${COLOR_INFO}Выполнение команды в контейнере PHP: $command${COLOR_RESET}"
    docker exec -i "${PROJECT_PREFIX}"_php bash -c "cd /var/www/html/; $command"
    return $?
}

# Функция для выполнения команд в контейнере MySQL
runInMySql() {
    local command=$@
    docker exec -i ${PROJECT_PREFIX}_mysql su mysql -c "$command"
    return $?
}

# Функция для применения дампа базы данных
applyDump() {
    cat $1 | docker exec -i ${PROJECT_PREFIX}_mysql mysql -u $MYSQL_USER -p"$MYSQL_PASSWORD" $MYSQL_DATABASE
    return $?
}

# Функция для создания дампа базы данных
makeDump() {
    runInMySql "export MYSQL_PWD='$MYSQL_PASSWORD'; mysqldump -u $MYSQL_USER $MYSQL_DATABASE" > $1
    return $?
}
function enterInPhp {
    docker exec -u www-data -it "${PROJECT_PREFIX}"_php bash
    return $?
}

# Функция для синхронизации файлов с сервера
syncFiles() {
    sshpass -p $REMOTE_SSH_PASS rsync -rzclEt -e 'ssh -p $REMOTE_SSH_PORT' --progress --delete-after --exclude='web_release' --exclude='backup' --exclude='cache' --exclude='cache' --exclude='.settings_extra.php' --exclude='.settings.php' --exclude='php_interface' $REMOTE_SSH_USER@$REMOTE_SSH_HOST:$REMOTE_BITRIX_URL $LOCAL_BITRIX_URL
    return $?
}

# Функция для синхронизации базы данных с сервера
syncDb() {
    makeDumpOnServer
    getDumpFromServer
    removeDumpFromServer
    loadDumpToDocker
}

# Функция для создания дампа на сервере
makeDumpOnServer() {
    sshpass -p $REMOTE_SSH_PASS ssh -p$REMOTE_SSH_PORT -o StrictHostKeyChecking=no -T $REMOTE_SSH_USER@$REMOTE_SSH_HOST <<-SSH
        if [ -f dump.sql.gz ]; then
            rm $REMOTE_MYSQL_DUMP_PATH
        fi
        mysqldump -h$REMOTE_MYSQL_HOST -u$REMOTE_MYSQL_USER -p'$REMOTE_MYSQL_PASS' $REMOTE_MYSQL_DB_NAME | gzip - > $REMOTE_MYSQL_DUMP_PATH
SSH
    return $?
}

# Функция для удаления дампа с сервера
removeDumpFromServer() {
    sshpass -p $REMOTE_SSH_PASS ssh -p $REMOTE_SSH_PORT -o StrictHostKeyChecking=no -T $REMOTE_SSH_USER@$REMOTE_SSH_HOST <<-SSH
        if [ -f dump.sql.gz ]; then
            rm $REMOTE_MYSQL_DUMP_PATH
        fi
SSH
    return $?
}

# Функция для получения дампа с сервера
getDumpFromServer() {
    sshpass -p "$REMOTE_SSH_PASS" rsync -rzclEt -e "ssh -p $REMOTE_SSH_PORT" --progress $REMOTE_SSH_USER@$REMOTE_SSH_HOST:$REMOTE_MYSQL_DUMP_PATH dump.sql.gz
    return $?
}

# Функция для загрузки дампа в Docker
loadDumpToDocker() {
    if [ -f dump.sql.gz ]; then
        docker/dctl.sh db import containers/mysql/drop_all_tables.sql
        gunzip -c dump.sql.gz | docker/dctl.sh db import -
    fi
    return $?
}

# Интерактивный режим
interactive_mode() {
    echo -e "${COLOR_WARNING}Добро пожаловать в интерактивный режим!${COLOR_RESET}"

    while true; do
        read -r -e -p  "Введите команду (help для справки, exit для выхода): " cmd
        case $cmd in
            help)
                echo -e "${COLOR_WARNING}ПОМОЩЬ:${COLOR_RESET}"
                echo "make env - скопировать .env.example в .env"
                echo "init - инициализировать проект и репозитории Bitrix"
                echo "make dump - создать дамп и отправить в репозиторий базы данных"
                echo "db import FILE - загрузить FILE в MySQL"
                echo "db renew - загрузить дамп из репозитория, обновить базу данных и применить"
                echo "db - запустить CLI базы данных"
                echo "db export > file.sql - экспортировать базу данных в файл"
                echo "build - собрать Docker-образы"
                echo "up - запустить Docker-контейнеры в режиме демона"
                echo "down - остановить и удалить Docker-контейнеры"
                echo "down full - остановить и удалить все Docker-контейнеры"
                echo "run - выполнить команду в контейнере PHP из корня проекта"
                echo "sync files - синхронизировать файлы с сервера"
                echo "sync db - синхронизировать базу данных с сервера"
                echo "sync upload - синхронизировать папку upload с сервера"
                echo "sync bitrix - синхронизировать папку Bitrix с сервера"
                echo "sync all - синхронизировать базу данных, папку upload и Bitrix с сервера"
                ;;
            exit)
                echo -e "${COLOR_INFO}Выход из интерактивного режима.${COLOR_RESET}"
                break
                ;;
            make\ env)
                cp .env.example .env
                echo -e "${COLOR_SUCCESS}Скопирован .env.example в .env${COLOR_RESET}"
                ;;
            init)
                if [ ! -d "../${PROJECT_PREFIX}" ]; then
                    git clone "$PROJECT_REPO" ../"${PROJECT_PREFIX}" || echo "Project repo not found"
                fi

                if [ ! -d "../${PROJECT_PREFIX}/bitrix" ]; then
                    git clone "$BITRIX_REPO" ../"${PROJECT_PREFIX}"/bitrix/ || echo "Bitrix repo not found"
                fi
                docker-compose -p "${PROJECT_PREFIX}" build
                docker-compose -p "${PROJECT_PREFIX}" up -d
                if [ ! -d "../${PROJECT_PREFIX}/${LOCAL_VENDOR_PATH}" ]; then
                    runInPhp "cd ${PROJECT_PREFIX}/local/php_interface/ && composer install"
                fi
                ;;
            make\ dump)
                git clone "$DATABASE_REPO" ../docker/data/mysql/dump || echo "not clone repo"
                makeDump ../docker/data/mysql/dump/database.sql
                cd ../docker/data/mysql/dump
                git add database.sql
                git commit -a -m 'update database'
                git push origin master
                echo "PUSH SUCCESS"
                ;;
            db\ import\ *)
                file=$(echo "$cmd" | awk '{print $3}')
                applyDump "$file"
                ;;
            db\ renew)
                rm -rf "../docker/data/mysql/dump" || echo "old dump not found"
                git clone "$DATABASE_REPO" ../docker/data/mysql/dump
                applyDump "../docker/containers/mysql/drop_all_tables.sql"
                applyDump "../docker/data/mysql/dump/database.sql"
                ;;
            db)
                docker exec -it "${PROJECT_PREFIX}"_mysql mysql -u $MYSQL_USER -p"$MYSQL_PASSWORD" $MYSQL_DATABASE
                ;;
            db\ export)
                runInMySql "export MYSQL_PWD='$MYSQL_PASSWORD'; mysqldump -u $MYSQL_USER $MYSQL_DATABASE"
                ;;
            build)
                docker-compose -p "${PROJECT_PREFIX}" build
                ;;
            up)
                docker-compose -p "${PROJECT_PREFIX}" up -d
                ;;
            down)
                docker-compose -p "${PROJECT_PREFIX}" down
                ;;
            down\ full)
                docker stop $(docker ps -q)
                ;;
            run\ *)
                command=$(echo "$cmd" | awk '{print $2}')
                runInPhp "$command"
                ;;
            sync\ files)
                syncFiles
                ;;
            sync\ db)
                syncDb
                ;;
            sync\ upload)
                sshpass -p $REMOTE_SSH_PASS rsync -rzclEt -e "ssh -p $REMOTE_SSH_PORT" --progress --delete-after --exclude='*.gz' $REMOTE_SSH_USER@$REMOTE_SSH_HOST:$REMOTE_UPLOAD_URL $LOCAL_UPLOAD_URL
                ;;
            sync\ bitrix)
                syncFiles
                ;;
            sync\ all)
                syncDb
                syncFiles
                sshpass -p "$REMOTE_SSH_PASS" rsync -rzclEt -e "ssh -p $REMOTE_SSH_PORT" --progress --delete-after --exclude='*.gz' "$REMOTE_SSH_USER"@"$REMOTE_SSH_HOST":"$REMOTE_UPLOAD_URL" "$LOCAL_UPLOAD_URL"
                ;;
            *)
                echo -e "${COLOR_ERROR}Неизвестная команда: $cmd${COLOR_RESET}"
                ;;
        esac
    done
}

# Обрабатываем команды
if [ $# -eq 0 ]; then
    interactive_mode
else
    case "$1" in
        "make")
            case "$2" in
                "env")
                    cp .env.example .env
                    echo -e "${COLOR_SUCCESS}Скопирован .env.example в .env${COLOR_RESET}"
                    ;;
                "dump")
                    git clone "$DATABASE_REPO" ../docker/data/mysql/dump || echo "not clone repo"
                    makeDump ../docker/data/mysql/dump/database.sql
                    cd ../docker/data/mysql/dump
                    git add database.sql
                    git commit -a -m 'update database'
                    git push origin master
                    echo "PUSH SUCCESS"
                    ;;
                *)
                    echo -e "${COLOR_ERROR}Неизвестная подкоманда: $2${COLOR_RESET}"
                    ;;
            esac
            ;;
        "sync")
            case "$2" in
                "files")
                    syncFiles
                    ;;
                "db")
                    syncDb
                    ;;
                "upload")
                    sshpass -p "$REMOTE_SSH_PASS" rsync -rzclEt -e "ssh -p $REMOTE_SSH_PORT" --progress --delete-after --exclude='*.gz' "$REMOTE_SSH_USER"@"$REMOTE_SSH_HOST":"$REMOTE_UPLOAD_URL" "$LOCAL_UPLOAD_URL"
                    ;;
                "bitrix")
                    syncFiles
                    ;;
                "all")
                    syncDb
                    syncFiles
                    sshpass -p "$REMOTE_SSH_PASS" rsync -rzclEt -e "ssh -p $REMOTE_SSH_PORT" --progress --delete-after --exclude='*.gz' "$REMOTE_SSH_USER"@"$REMOTE_SSH_HOST":"$REMOTE_UPLOAD_URL" "$LOCAL_UPLOAD_URL"
                    ;;
                *)
                    echo -e "${COLOR_ERROR}Неизвестная подкоманда: $2${COLOR_RESET}"
                    ;;
            esac
            ;;
        "db")
            case "$2" in
                "")
                    docker exec -it "${PROJECT_PREFIX}"_mysql mysql -u "$MYSQL_USER" -p"$MYSQL_PASSWORD" "$MYSQL_DATABASE"
                    ;;
                "export")
                    runInMySql "export MYSQL_PWD='$MYSQL_PASSWORD'; mysqldump -u $MYSQL_USER $MYSQL_DATABASE"
                    ;;
                "import")
                    applyDump $3
                    ;;
                "renew")
                    rm -rf "../docker/data/mysql/dump" || echo "old dump not found"
                    git clone "$DATABASE_REPO" ../docker/data/mysql/dump
                    applyDump "../docker/containers/mysql/drop_all_tables.sql"
                    applyDump "../docker/data/mysql/dump/database.sql"
                    ;;
                *)
                    echo -e "${COLOR_ERROR}Неизвестная подкоманда: $2${COLOR_RESET}"
                    ;;
            esac
            ;;
        "build")
            docker-compose -p "${PROJECT_PREFIX}" build
            ;;
        "init")
            if [ ! -d "../${PROJECT_PREFIX}" ]; then
                git clone "$PROJECT_REPO" ../"${PROJECT_PREFIX}" || echo "Project repo not found"
            fi

            if [ ! -d "../${PROJECT_PREFIX}/bitrix" ]; then
                git clone "$BITRIX_REPO" ../"${PROJECT_PREFIX}"/bitrix/ || echo "Bitrix repo not found"
            fi
            docker-compose -p "${PROJECT_PREFIX}" build
            docker-compose -p "${PROJECT_PREFIX}" up -d
            if [ ! -d "../${PROJECT_PREFIX}/${LOCAL_VENDOR_PATH}" ]; then
                runInPhp "cd ${PROJECT_PREFIX}/local/php_interface/ && composer install"
            fi
            ;;
        "up")
#            docker-compose -p "${PROJECT_PREFIX}" build
            docker-compose -p "${PROJECT_PREFIX}" up -d
            ;;
        "in")
            enterInPhp "${@:2}"
            ;;
        "down")
            case "$2" in
                "full")
                    docker stop $(docker ps -q)
                    ;;
                "")
                    docker-compose -p "${PROJECT_PREFIX}" down
                    ;;
                *)
                    echo -e "${COLOR_ERROR}Неизвестная подкоманда: $2${COLOR_RESET}"
                    ;;
            esac
            ;;
        "run")
            if [ "$2" == "" ]; then
                docker exec -u www-data -it "${PROJECT_PREFIX}"_php bash
            else
                runInPhp "${@:2}"
            fi
            ;;
        *)
            echo -e "${COLOR_ERROR}Неизвестная команда: $1${COLOR_RESET}"
            ;;
    esac
fi

# Установка цветов и автодополнения
set_terminal_colors
