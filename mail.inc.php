<?php
function utf8_mail($to, $title, $content) {
    $mail_headers  = "Content-type: text/plain; charset=utf-8\r\n";
    $mail_headers .= "From: Service Monitor <nobody@blog.ustc.edu.cn>\r\n";

    $title = '=?utf-8?B?'.base64_encode($title).'?=';
    mail($to, $title, $content, $mail_headers);
}
