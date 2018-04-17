<?php
#criando método para minerar dados
/*
 * Para montar o script de mineração utilizei o CURL, para manipular uma pesquisa via POST na seguinte URL: http://www.scf2.sebrae.com.br/portalcf/
 * 1 - primeiro fiz a requisição como o browse faz
 * 2 - depois dei um load no html que ele me retornou
 * 3 - utilizei o simple html dom para que eu possa manipular melhor os dados de retorno
 * Como não há uma regra de negócio definida fiz tudo na controller e a chamada na view index.php
 */
namespace estoque\Http\Controllers;
$biblioteca  = $_SERVER['DOCUMENT_ROOT'].'\..\vendor\simple_html_dom.php';
include($biblioteca);
class MineracaoController extends Controller{
   
    public function downloadUrl(){
        if (!function_exists('curl_init')){ 
            die('CURL is not installed!');
        }
       $ch = curl_init('http://www.scf2.sebrae.com.br/portalcf/');
        curl_setopt($ch, CURLOPT_VERBOSE, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.0; en-US; rv:1.7.12) Gecko/20050915 Firefox/1.0.7');
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array());
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        //curl_setopt($ch, CURLOPT_STDERR, $out);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $buf = curl_exec($ch);
        $params = array();
        $doc = new \DOMDocument();
        $doc->loadHTML($buf);
        foreach ($doc->getElementsByTagName('input') as $element){
            $params[$element->getAttribute('name')] = $element->getAttribute('value');
           
        }
        curl_close($ch);
        $params['ctl00$ContentPlaceHolder1$ddModalidade'] = 11;
        $params['ctl00$ContentPlaceHolder1$ddAno'] = 2017;
        $chamada = curl_init('http://www.scf2.sebrae.com.br/portalcf/');
        curl_setopt($chamada, CURLOPT_VERBOSE, true);
        curl_setopt($chamada, CURLOPT_POST, true);
        curl_setopt($chamada, CURLOPT_POSTFIELDS, $params);
        curl_setopt($chamada, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.0; en-US; rv:1.7.12) Gecko/20050915 Firefox/1.0.7');
        curl_setopt($chamada, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($chamada, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($chamada, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($chamada, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($chamada, CURLOPT_REFERER,'http://www.scf2.sebrae.com.br/portalcf' );
        curl_setopt($chamada, CURLOPT_HEADER, array(
           "Accept:text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8",
            "Accept-Encoding:gzip, deflate, br",
            "Accept-Language:en-US,en;q=0.8",
           "Cache-Control:max-age=0",
            "Connection:keep-alive",
            "Content-Length:20078",
            "Content-Type:application/x-www-form-urlencoded"
        ));
        //curl_setopt($ch, CURLOPT_STDERR, $out);
        curl_setopt($chamada, CURLOPT_FOLLOWLOCATION, true);
        $result = curl_exec($chamada);  
        $docu = new \DOMDocument("1.0", "ISO-8859-1" );
        libxml_use_internal_errors(true);
        $docu->loadHTML(mb_convert_encoding($result, 'HTML-ENTITIES', 'ISO-8859-1'));
        libxml_clear_errors();
        $html = str_get_html($result);
        foreach ($html->find('div[class=form-searchresult]') as $value) {
           echo $value->innertext;
        }
        curl_close($chamada);
        exit;
       return view('index')->with('value',$value);
    }
      
    
  
}