<?php
/**
 * Phanbook : Delightfully simple forum software
 *
 * Licensed under The GNU License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @link    http://phanbook.com Phanbook Project
 * @since   1.0.0
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt
 */
namespace Phanbook\Mail;

use Phalcon\Mvc\User\Component;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Model\Validator\Email as Email;
use Phanbook\Models\Template;

require_once ROOT_DIR . '/vendor/swiftmailer/swiftmailer/lib/swift_required.php';

use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;

class Mail extends Component
{
    protected $transport;


    private function getTemplate($key, $params)
    {
        if (!isset($params['subject'])) {
            $subject = $this->config->application->name;
        } else {
            $subject = $params['subject'];
        }

        //Set views layout
        $this->view->setViewsDir(app_path('core/views/'));
        $render = $this->view->getRender(
            rtrim($this->config->mail->templatesDir, '/'),
            $key,
            $params,
            function ($view) {
                $view->setRenderLevel(View::LEVEL_LAYOUT);
            }
        );
        //echo $render;die();

        if (!empty($render)) {
            return $render;
        }
        //When use template for cli
        return $this->view->getContent();
    }

    /**
     * Send email
     *
     * @param $to - email to send
     * @param $templateKey - a unique key of the template
     * @param array $params - array with params for template. If $params['subject'] not set we get subject from database;
     *
     * @return int
     */
    public function send($to, $templateKey, $params = [])
    {
        $body = $this->getTemplate($templateKey, $params);
        if (!$body) {
            d('You need to create templates email in database');
            return false;
        }

        if (!isset($params['subject'])) {
            $subject = $this->config->application->name;
        } else {
            $subject = $params['subject'];
        }

        // Create the message
        $message = Swift_Message::newInstance()
            ->setSubject($subject)
            ->setTo($to)
            ->setFrom([$this->config->mail->fromEmail => $this->config->mail->fromName])
            ->setBody($body, 'text/html');
        if (!$this->transport) {
            $this->transport = Swift_SmtpTransport::newInstance(
                $this->config->mail->smtp->server,
                $this->config->mail->smtp->port
            )
                ->setUsername($this->config->mail->smtp->username)
                ->setPassword($this->config->mail->smtp->password);
        }

        $mailer = Swift_Mailer::newInstance($this->transport);
        return $mailer->send($message);
    }

    /**
     * Send a test email
     *
     * @param $to
     *
     * @return int
     */
    public function sendTest($to)
    {
        return $this->send($to, 'test');
    }
    /**
     * Send a test email
     *
     * @param $to
     *
     * @return int
     */
    public function getToMailTest($to)
    {
        return $to;
    }
    public function renderTest()
    {

        return $this->getTemplate('test', []);
    }
}
