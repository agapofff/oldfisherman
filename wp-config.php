<?php
/**
 * Основные параметры WordPress.
 *
 * Скрипт для создания wp-config.php использует этот файл в процессе установки.
 * Необязательно использовать веб-интерфейс, можно скопировать файл в "wp-config.php"
 * и заполнить значения вручную.
 *
 * Этот файл содержит следующие параметры:
 *
 * * Настройки базы данных
 * * Секретные ключи
 * * Префикс таблиц базы данных
 * * ABSPATH
 *
 * @link https://ru.wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Параметры базы данных: Эту информацию можно получить у вашего хостинг-провайдера ** //
/** Имя базы данных для WordPress */
define( 'DB_NAME', 'ribica' );

/** Имя пользователя базы данных */
define( 'DB_USER', 'ribica' );

/** Пароль к базе данных */
define( 'DB_PASSWORD', '5ewcM02CrfsrdKAZ' );

/** Имя сервера базы данных */
define( 'DB_HOST', 'localhost' );

/** Кодировка базы данных для создания таблиц. */
define( 'DB_CHARSET', 'utf8mb4' );

/** Схема сопоставления. Не меняйте, если не уверены. */
define( 'DB_COLLATE', '' );

/**#@+
 * Уникальные ключи и соли для аутентификации.
 *
 * Смените значение каждой константы на уникальную фразу. Можно сгенерировать их с помощью
 * {@link https://api.wordpress.org/secret-key/1.1/salt/ сервиса ключей на WordPress.org}.
 *
 * Можно изменить их, чтобы сделать существующие файлы cookies недействительными.
 * Пользователям потребуется авторизоваться снова.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '-)B16+@GCvPpV~X^(yaUe8fMt<yVo3_*>tpj +d_81+Er(}_#hLy1!Q`.>Ig;vt2' );
define( 'SECURE_AUTH_KEY',  ';Z4fA9*h(t>3kA:Z$aNf|zX9ae~)~0xcb`#:hyr6CeVrUN`uvI.KXEGK2E_[?M8y' );
define( 'LOGGED_IN_KEY',    'rJG9+*;{.~1dg+g-Z6a@q<q|+w`|-Z>OkQWfV6VkR*UX{$~zg+JhMKj<0xWz+Cdb' );
define( 'NONCE_KEY',        '[SRZf-cF-f$q=D)zX9s=rW/L2rVm:Q#0~(j0X(n0x!s 9L:|^:?OfJ>Hs-|8|HNo' );
define( 'AUTH_SALT',        'a&Esw`PYL6$QdTF7DY!bmwRQg{}~-e+r~>tdj7AZAGK9_R^=+yr{kYK>:hmRMBnL' );
define( 'SECURE_AUTH_SALT', 'x3=vZuDA6eIrW;;h!sy,G2uX_>viq9fSYP~j:A)X4/-Ae6)K>nas<HutOtQ@)GHm' );
define( 'LOGGED_IN_SALT',   '^:Ierg;yNft_`shE65=m`@X[mDlfNlt|Kq1`ws4?f.zy.FsfDTPFbMh~OT=SN^aj' );
define( 'NONCE_SALT',       'rwYgfp#:_+xJ@<;@uVlp(0I:*.iy5coUe^Ul_Bh54[!z+?a7.f~H=A[zGF@t2Px6' );

/**#@-*/

/**
 * Префикс таблиц в базе данных WordPress.
 *
 * Можно установить несколько сайтов в одну базу данных, если использовать
 * разные префиксы. Пожалуйста, указывайте только цифры, буквы и знак подчеркивания.
 */
$table_prefix = 'wp_';

/**
 * Для разработчиков: Режим отладки WordPress.
 *
 * Измените это значение на true, чтобы включить отображение уведомлений при разработке.
 * Разработчикам плагинов и тем настоятельно рекомендуется использовать WP_DEBUG
 * в своём рабочем окружении.
 *
 * Информацию о других отладочных константах можно найти в документации.
 *
 * @link https://ru.wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Произвольные значения добавляйте между этой строкой и надписью "дальше не редактируем". */



/* Это всё, дальше не редактируем. Успехов! */

/** Абсолютный путь к директории WordPress. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Инициализирует переменные WordPress и подключает файлы. */
require_once ABSPATH . 'wp-settings.php';
