
# Table Master

**Table Master** – это веб-приложение на базе Laravel 12, позволяющее пользователям загружать Excel-файлы, выполнять их конвертацию в различные форматы (XLS, XLSX, CSV, ODS, HTML) и слияние(только XLS, XLSX). Приложение использует асинхронную обработку задач посредством очередей для выполнения конвертаций с использованием библиотек PhpSpreadsheet и OpenSpout.

## Функциональные возможности

### Загрузка файлов

-   Пользователь может загрузить Excel-файл (форматы XLS и XLSX) через веб-интерфейс
-   Загруженный файл сохраняется на локальном диске, а его метаданные – в базе данных MySql

### Конвертация файлов

Приложение поддерживает несколько вариантов конвертации:

-   XLSX → XLS
-   XLS → XLSX 
-   XLSX/XLS → ODS
-   XLSX/XLS → CSV  (одиночный файл либо ZIP-архив для нескольких листов)
-   XLSX/XLS → TSV  (одиночный файл либо ZIP-архив для нескольких листов)
-   XLSX/XLS → HTML (одиночный файл либо ZIP-архив для нескольких листов)

### Слияние файлов

-   Для файлов формата XLS и XLSX существует фозможность слияние листков в один файл с сохранением стилей и форматирования

Все конвертации выполняются асинхронно с помощью Laravel Job. После завершения конвертации обновляются статус и путь к результату в базе данных.

### Скачивание результатов

После успешной конвертации готовый файл автоматически скачивается в браузере.

### Проверка статуса

Реализована функция периодической проверки статуса конвертации (через AJAX), позволяющая уведомлять пользователя о состоянии задачи.

## Установка и настройка

### Требования

-   PHP 8.2+
-   Composer
-   Laravel 11+
-   Установленный XAMMP
-   git

### Шаги установки

1. **Установить последнюю версию XAMMP**

2. **В папке xampp\htdocs выполнить клонирование репозитория**
    ```bash
    git clone https://github.com/Dmitr15/table-master.git
    ```

3. **Установка зависимостей через Composer**
    ```bash
    composer install
    ```
    
4. **Выполните миграции для создания необходимых таблиц**
    ```bash
    php artisan migrate
    ```

5. **Запустить клиентскую часть в отдельном терминале**
    ```bash
    npm run dev
    ```
    
6. **Запустить сервер в отдельном терминале**
    ```bash
    php artisan serve
    ```
    
7. **Настройка очереди, в отдельном терминале**
    ```bash
    php artisan queue:work
    ```

    ## Экранные формы

    ### Страница регистрации
    <img width="1909" height="953" alt="image" src="https://github.com/user-attachments/assets/7864ec12-b84d-4a1f-9076-a59988f7c5a4" />

    ### Страница логина
    <img width="1908" height="953" alt="image" src="https://github.com/user-attachments/assets/0c8eb299-fe35-4d66-9024-31a6e0c4ee58" />

    ### Главная страница

    <img width="1897" height="942" alt="image" src="https://github.com/user-attachments/assets/ddc01ff5-baba-48d4-b406-b28772c9ae70" />
    <img width="1899" height="950" alt="image" src="https://github.com/user-attachments/assets/75e1ef3c-0dc1-45b8-a90b-122a3c2a2a07" />
    <img width="1900" height="951" alt="image" src="https://github.com/user-attachments/assets/188ffeee-4c6c-4046-8d73-7b588f023e0f" />
    <img width="1898" height="950" alt="image" src="https://github.com/user-attachments/assets/4a8c9c2a-e637-4213-9753-3d85a2cfac03" />
    
    ### Страница для работы с файлами
    <img width="1898" height="953" alt="image" src="https://github.com/user-attachments/assets/19ff4337-aec8-43ff-93e0-42e4a248714e" />
