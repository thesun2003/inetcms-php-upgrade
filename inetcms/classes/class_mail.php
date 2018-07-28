<?
class Tmail {
    function Tmail($to, $subject, $mess_body, $fromName, $fromMail) {
        $this->to = $to;
        $this->subj = $subject;
        $this->body = $mess_body;
        $this->from = $fromName;
        $this->fmail = $fromMail;

        $this->headers = 'MIME-Version: 1.0'."\r\n".
           'Content-type: text/html; charset=UTF-8'."\r\n".
           'From: "' . $this->from . '" <' . $this->fmail . ">\r\n" .
           # 'BCC: asterix@softservice.org'."\r\n".
           'X-Mailer: PHP/' . phpversion();
    }

    function send() {
        mail($this->to, $this->subj, $this->body, $this->headers);        
    }
}
?>