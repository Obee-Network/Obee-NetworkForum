<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInit496b3f0d0272c0304e6d339e33378ebd
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        spl_autoload_register(array('ComposerAutoloaderInit496b3f0d0272c0304e6d339e33378ebd', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader();
        spl_autoload_unregister(array('ComposerAutoloaderInit496b3f0d0272c0304e6d339e33378ebd', 'loadClassLoader'));

        $useStaticLoader = PHP_VERSION_ID >= 50600 && !defined('HHVM_VERSION') && (!function_exists('zend_loader_file_encoded') || !zend_loader_file_encoded());
        if ($useStaticLoader) {
            require_once __DIR__ . '/autoload_static.php';

            call_user_func(\Composer\Autoload\ComposerStaticInit496b3f0d0272c0304e6d339e33378ebd::getInitializer($loader));
        } else {
            $map = require __DIR__ . '/autoload_namespaces.php';
            foreach ($map as $namespace => $path) {
                $loader->set($namespace, $path);
            }

            $map = require __DIR__ . '/autoload_psr4.php';
            foreach ($map as $namespace => $path) {
                $loader->setPsr4($namespace, $path);
            }

            $classMap = require __DIR__ . '/autoload_classmap.php';
            if ($classMap) {
                $loader->addClassMap($classMap);
            }
        }

        $loader->register(true);

        return $loader;
    }
}

$PHPMAILER_LANG['authenticate']         = 'Erro do SMTP: Não foi possível realizar a autenticação.';
$PHPMAILER_LANG['connect_host']         = 'Erro do SMTP: Não foi possível realizar ligação com o servidor SMTP.';
$PHPMAILER_LANG['data_not_accepted']    = 'Erro do SMTP: Os dados foram rejeitados.';
$PHPMAILER_LANG['empty_message']        = 'A mensagem no e-mail está vazia.';
$PHPMAILER_LANG['encoding']             = 'Codificação desconhecida: ';
$PHPMAILER_LANG['execute']              = 'Não foi possível executar: ';
$PHPMAILER_LANG['file_access']          = 'Não foi possível aceder o ficheiro: ';
$PHPMAILER_LANG['file_open']            = 'Abertura do ficheiro: Não foi possível abrir o ficheiro: ';
$PHPMAILER_LANG['from_failed']          = 'Ocorreram falhas nos endereços dos seguintes remententes: ';
$PHPMAILER_LANG['instantiate']          = 'Não foi possível iniciar uma instância da função mail.';
$PHPMAILER_LANG['invalid_address']      = 'Não foi enviado nenhum e-mail para o endereço de e-mail inválido: ';
$PHPMAILER_LANG['mailer_not_supported'] = ' mailer não é suportado.';
$PHPMAILER_LANG['provide_address']      = 'Tem de fornecer pelo menos um endereço como destinatário do e-mail.';
$PHPMAILER_LANG['recipients_failed']    = 'Erro do SMTP: O endereço do seguinte destinatário falhou: ';
$PHPMAILER_LANG['signing']              = 'Erro ao assinar: ';
$PHPMAILER_LANG['smtp_connect_failed']  = 'SMTP Connect() falhou.';
$PHPMAILER_LANG['smtp_error']           = 'Erro de servidor SMTP: ';
$PHPMAILER_LANG['variable_set']         = 'Não foi possível definir ou redefinir a variável: ';
$PHPMAILER_LANG['extension_missing']    = 'Extensão em falta: ';
