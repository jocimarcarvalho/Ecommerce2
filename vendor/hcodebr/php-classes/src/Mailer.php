<?php

namespace Lexter;

use Rain\Tpl;

class Mailer {

    const USERNAME = "";
    const PASSWORD =  "";
    const NAME_FROM = "";

    private   $mail;

    public function __construct($toAddress, $toName, $subject, $tplName, $data = array())
    {

        $config = array(
            "tpl_dir"       => $_SERVER["DOCUMENT_ROOT"] . "/views/email/",
             "cache_dir"     => $_SERVER["DOCUMENT_ROOT"] ."\/views-cache\/",
              "debug"         => false // set to false to improve the speed
     );

        Tpl::configure( $config );

    $tpl = new Tpl;

    foreach($data as $key => $value)
    {
        $tpl->assign($key, $value);
    } 

    $html = $tpl->draw($tplName, true); // 'true' , pra jogar os dados na variável e não na tela

        $this->mail = new \PHPMailer;
        try {
            //Server settings
              $this->mail->SMTPDebug = 0;                                       // Enable verbose debug output
              $this->mail->isSMTP();                                            // Set mailer to use SMTP
              $this->mail->Host       = 'smtp.gmail.com';  // Specify main and backup SMTP servers
              $this->mail->SMTPAuth   = true;                                   // Enable SMTP authentication
              $this->mail->Username   = Mailer::USERNAME;                     // SMTP username
              $this->mail->Password   = Mailer::PASSWORD;                               // SMTP password
              $this->mail->SMTPSecure = 'tls';                                  // Enable TLS encryption, `ssl` also accepted
              $this->mail->Port       = 587 ;                             // TCP port to connect to
        
            //Recipients
              $this->mail->setFrom(Mailer::USERNAME, Mailer::NAME_FROM);
              $this->mail->addAddress($toAddress, $toName);    // Add a recipient
            //  $this->mail->addAddress('ellen@example.com');               // Name is optional
            //  $this->mail->addReplyTo('info@example.com', 'Information');
            //  $this->mail->addCC('cc@example.com');
            //  $this->mail->addBCC('bcc@example.com');
        
            // Attachments
            //  $this->mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
            //  $this->mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
        
            // Content
              $this->mail->isHTML(true);                                  // Set email format to HTML
              $this->mail->Subject = $subject;
              $this->mail->Body    = $html;
              $this->mail->AltBody = 'Obrigado por participar';
              //$this->mail->msgHTML($html);
        
             

        } catch (Exception $e) {
            echo "Erro ao enviar o email: {  $this->mail->ErrorInfo}";
        }
    }

        public function send()
        {
            return $this->mail->send();
        }
    
}
?>