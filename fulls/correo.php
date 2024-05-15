<?
/*
formail completo, � possibile aggiungere qualunque campo
� sufficiente inserire le due pagine in un punto qualunque
del proprio dominio.
by linkbruttocane
*/

//INIZIO PARAMETRI DA SETTARE OBBLIGATORIAMENTE
/****************************************************************************************/
// Il parametro $delay indica i secondi di ritardo impiegati 
// a riportare l'utente all'home page dopo che abbia 
// compilato correttamente il modulo

$delay = "5";

// Il parametro $url indica la pagina alla quale si viene
// rimandati una volta compilato correttamente il modulo
// io ho messo una homepage, ma potrebbe essere qualunque altra pagina

$url = "http://www.fundacioferrerpastor.org/index.html";

// Il parametro $provenienza indica le possibili provenienze dei dati: indicare
// il proprio dominio nella forma mostrata dall'esempio

$provenienza = array ('fundacioferrerpastor.org','www.fundacioferrerpastor.org','217.76.130.58');

// Il parametro $esclusioni vi permette di NON consentire 
// messaggi da un indirizzo mail specificato
// sia appartenente ad un dominio, ovvero 
// 'tutte le mail che appartengono ad un dominio'
// od anche a singoli account

$esclusioni = array ('*@quellochetipare.com', 'nomechetipare@dominio.com', 'altro@dominio.com');


//FINE PARAMETRI DA SETTARE OBBLIGATORIAMENTE
/****************************************************************************************/



$versione_form = "stabile";

function print_error($reason,$type = 0) {
   global $versione_form;
   build_body($title, $bgcolor, $text_color, $link_color, $vlink_color, $alink_color, $style_sheet);
      if ($type == "missing") {
      ?>
      <body bgcolor='#94B6C6'>
      <p align='center'><font face='Arial' color='#990000' size='2'>El correu no a estat enviat per les seg�ents causes...</font></p><br>
<p align='center'><font face='Arial' size='2' color='#990000'>
     <?
     echo $reason."\n";
     ?>
     </font>
     <p align='center'><font face='Arial' size='2' color='#990000'>Actualitze el seu navegador.</font></p><?
   } else { // every other error
      ?>
      El correu no a estat enviat per les seg�ents causes...<p align='center'>
      <?
   }
   echo "<br><br>\n";
  
   exit;
}


function check_banlist($esclusioni, $email) {
   if (count($esclusioni)) {
      $allow = true;
      foreach($esclusioni as $banned) {
         $temp = explode("@", $banned);
         if ($temp[0] == "*") {
            $temp2 = explode("@", $email);
            if (trim(strtolower($temp2[1])) == trim(strtolower($temp[1])))
               $allow = false;
         } else {
            if (trim(strtolower($email)) == trim(strtolower($banned)))
               $allow = false;
         }
      }
   }
   if (!$allow) {
      print_error("Esta usando una <b>direccion de email escluido.</b>");
   }
}


function check_referer($provenienza) {
   if (count($provenienza)) {
      $found = false;
      $temp = explode("/",getenv("HTTP_REFERER"));
      $referer = $temp[2];
      for ($x=0; $x < count($provenienza); $x++) {
         if (eregi ($provenienza[$x], $referer)) {
            $found = true;
         }
      }
      if (!getenv("HTTP_REFERER"))
         $found = false;
      if (!$found){
         print_error("Provienes de un <b>dominio no autorizado.</b>");
         error_log("[FormMail.php] Illegal Referer. (".getenv("HTTP_REFERER").")", 0);
      }
         return $found;
      } else {
         return true; // 
   }
}
if ($provenienza)
   check_referer($provenienza);

if ($esclusioni)
   check_banlist($esclusioni, $email);


function parse_form($array) {
   // build reserved keyword array
   $reserved_keys[] = "required";
   $reserved_keys[] = "redirect";
   $reserved_keys[] = "email";
   $reserved_keys[] = "require";
   $reserved_keys[] = "contenitore";
   $reserved_keys[] = "titolo";
   $reserved_keys[] = "bgcolor";
   $reserved_keys[] = "text_color";
   $reserved_keys[] = "link_color";
   $reserved_keys[] = "vlink_color";
   $reserved_keys[] = "alink_color";
   $reserved_keys[] = "title";
   $reserved_keys[] = "missing_fields_redirect";
   $reserved_keys[] = "invia_Dati";
   if (count($array)) {
      while (list($key, $val) = each($array)) {
       
         $reserved_violation = 0;
         for ($ri=0; $ri<count($reserved_keys); $ri++) {
            if ($key == $reserved_keys[$ri]) {
               $reserved_violation = 1;
            }
         }
      
         if ($reserved_violation != 1) {
            if (is_array($val)) {
               for ($z=0;$z<count($val);$z++) {
                  $content .= "$key: $val[$z]\n";
               }
            } else {
               $content .= "$key: $val\n";
            }
         }
      }
   }
   return $content;
}


function mail_it($content, $titolo, $email, $contenitore) {
        mail($contenitore, $titolo, $content, "From: $email\r\nReply-To: $email\r\nX-Mailer: DT_formmail");
}


function build_body($title, $bgcolor, $text_color, $link_color, $vlink_color, $alink_color, $style_sheet) {
   if ($style_sheet)
      echo "<LINK rel=STYLESHEET href=\"$style_sheet\" Type=\"text/css\">\n";
   if ($title)
      echo "<title>$title</title>\n";
   if (!$bgcolor)
      $bgcolor = "#94B6C6";
   if (!$text_color)
      $text_color = "#80000";
   if (!$link_color)
      $link_color = "#0000FF";
   if (!$vlink_color)
      $vlink_color = "#FF0000";
   if (!$alink_color)
      $alink_color = "#000088";
   if ($background)
      $background = "background=\"$background\"";
   echo "<body bgcolor=\"$bgcolor\" text=\"$text_color\" link=\"$link_color\" vlink=\"$vlink_color\" alink=\"$alink_color\" $background>\n\n";
}

$contenitore = "triangles@triangles.es";

$contenitore_finale = split(',',$contenitore);
for ($i=0;$i<count($contenitore_finale);$i++) {
   $contenitore_to_test = trim($contenitore_finale[$i]);
   if (!eregi("^[_\\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\\.)+[a-z]{2,3}$", $contenitore_to_test)) {
      print_error("<b>Aquesta adre�a electr�nica no �s correcta  ($contenitore_to_test) </b>");
   }
}


if ($required)
   $require = $required;

if ($require) {
  
   $require = ereg_replace( " +", "", $require);
   $required = split(",",$require);
   for ($i=0;$i<count($required);$i++) {
      $string = trim($required[$i]);
   
      if((!(${$string})) || (!(${$string}))) {
       
         if ($missing_fields_redirect) {
            header ("Location: $missing_fields_redirect");
            exit;
         }
         $require;
         $missing_field_list .= "<b>Non trovato: $required[$i]</b><br>\n";
      }
   }
   
   if ($missing_field_list)
      print_error($missing_field_list,"missing");
}


if (($email) || ($EMAIL)) {
   $email = trim($email);
   if ($EMAIL)
      $email = trim($EMAIL);
   if (!eregi("^[_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,3}$", $email)) {
      print_error("El seu <b>correu elect�nic</b> no �s correcte");
   }
   $EMAIL = $email;
}






$content = parse_form($HTTP_POST_VARS);




if ($invia_Dati) {
   $invia_Dati = ereg_replace( " +", "", $invia_Dati);
   $splitta_Dati = split(",",$invia_Dati);
   $content .= "\n------ variabili utente ------\n";
   for ($i=0;$i<count($splitta_Dati);$i++) {
      $string = trim($splitta_Dati[$i]);
      if ($splitta_Dati[$i] == "REMOTE_HOST")
         $content .= "REMOTE HOST: ".$REMOTE_HOST."\n";
      else if ($splitta_Dati[$i] == "REMOTE_USER")
         $content .= "REMOTE USER: ". $REMOTE_USER."\n";
      else if ($splitta_Dati[$i] == "REMOTE_ADDR")
         $content .= "REMOTE ADDR: ". $REMOTE_ADDR."\n";
      else if ($splitta_Dati[$i] == "HTTP_USER_AGENT")
         $content .= "BROWSER: ". $HTTP_USER_AGENT."\n";
   }
}


if (!$titolo)
   $titolo = "Consulta desde la web";

mail_it(stripslashes($content), stripslashes($titolo), $email, $contenitore);


if ($redirect) {
   header ("Location: $redirect");
   exit;
} else {
   print "<body bgcolor='#94B6C6'>

<p align='center'><font face='Arial' size='2' color='#800000'>GRACIAS POR HABER CONTACTADO.</font></p><p align='center'><font face='Arial' size='2' color='#800000'>Recibir� la contestaci�n al correo indicado $email<br><br>...Retorno autom�tico a la p�gina en curso....</font></p><meta http-equiv='refresh' content='$delay; url=$url'>

<p align='center'>&nbsp;</p>

</body>
";
   echo "<br><br>\n";
   
   exit;
}

// <----------   fine    ----------> //  
?>