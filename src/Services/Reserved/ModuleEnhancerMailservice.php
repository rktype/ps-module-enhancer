<?php

namespace RkType\PSModuleEnhancer\Services\Reserved;

use Mail;
use Context;

class ModuleEnhancerMailservice
{

    /** @var int */
    protected $id_lang;

    /** @var string $template */
    protected $template;

    /** @var string $subject */
    protected $subject;

    /** @var string $template_vars */
    protected $template_vars;

    /** @var array $to */
    protected $to;

    /** @var array $to_name */
    protected $to_name = [];

    /** @var string $from */
    protected $from = null;

    /** @var string $from_name */
    protected $from_name = null;

    /** @var array $file_attachment */
    protected $file_attachment = null;

    /** @var bool $mode_smtp */
    protected $mode_smtp = null;

    /** @var string $template_path */
    protected $template_path = _PS_MAIL_DIR_;

    /** @var bool $die */
    protected $die = false;

    /** @var int $id_shop */
    protected $id_shop = null;

    /** @var string|array $bcc */
    protected $bcc = null;

    /** @var string $reply_to */
    protected $reply_to = null;


    public function __construct()
    {
    }

    public function idLang($id_lang)
    {
        $this->id_lang = $id_lang;
        return $this;
    }

    public function template($template)
    {
        $this->template = $template;
        return $this;
    }

    public function subject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    public function templateVars($template_vars)
    {
        foreach ($template_vars as $key => $template_var) {
            $this->template_vars['{'.rtrim(ltrim($key, '{'), '}').'}'] = $template_var;
        }
        return $this;
    }

    public function to($email, $name = '')
    {
        if(is_array($email)) {
            foreach ($email as $index => $addr) {
                $this->to($addr, is_array($name) && array_key_exists($index, $name) ? $name[$index] : '');
            }
        }else{
            $this->to[] = $email;
            $this->to_name[] = $name;
        }

        return $this;
    }

    public function from($email, $name = null)
    {
        $this->from = $email;
        $this->from_name = $name;
        return $this;
    }

    public function fileAttachment($attachment)
    {
        $this->file_attachment[] = $attachment;
        return $this;
    }

    public function modeSmtp($mode_smtp)
    {
        $this->mode_smtp = $mode_smtp;
        return $this;
    }

    public function templatePath($template_path)
    {
        $this->template_path = rtrim($template_path, '/') . '/';
        return $this;
    }

    public function dieAfterError($die)
    {
        $this->die = $die;
        return $this;
    }

    public function bcc($bcc)
    {
        $this->bcc = $bcc;
        return $this;
    }

    public function replyTo($reply_to)
    {
        $this->reply_to = $reply_to;
        return $this;
    }

    public function l($string, $id_lang = null, Context $context = null)
    {
        return Mail::l($string, $id_lang, $context);
    }

    public function send()
    {

        return Mail::Send(
            $this->id_lang,
            $this->template,
            $this->l($this->subject),
            $this->template_vars,
            $this->to,
            array_filter($this->to_name),
            $this->from,
            $this->from_name,
            $this->file_attachment,
            $this->mode_smtp,
            $this->template_path,
            $this->die,
            $this->id_shop
        );
    }
}
