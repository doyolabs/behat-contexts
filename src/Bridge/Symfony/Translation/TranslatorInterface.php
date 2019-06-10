<?php


namespace Doyo\Behat\Bridge\Symfony\Translation;



if(interface_exists('Symfony\Component\Translation\TranslatorInterface')){
    interface BaseTranslatorInterface extends \Symfony\Component\Translation\TranslatorInterface
    {
    }
}else{
    interface BaseTranslatorInterface extends \Symfony\Contracts\Translation\TranslatorInterface
    {

    }
}

interface TranslatorInterface extends BaseTranslatorInterface
{
}
