<?php 

    $text = file_get_contents("datas.txt");
    
    $regex = "/.+\s*\n/";
    $result2 = preg_match_all($regex, $text, $source);

    //Deleted datas before this text
    $strString = "Mais relevantes";

    for($i=0; $i<count($source[0]); $i++) {
        if(strcmp(trim($source[0][$i]), $strString) != 0) {
            unset($source[0][$i]);
        } else if(strcmp(trim($source[0][$i]), $strString) == 0) {
            unset($source[0][$i]);
            break;
        }
    }

    $strString = "O frete grátis está sujeito ao peso, preço e distância do envio.";
    
    $source[0] = array_values($source[0]);

    for($i=count($source[0])-1; $i>0; $i--) {
        if(strcmp(trim($source[0][$i]), $strString) != 0) {
            unset($source[0][$i]);
        } else if(strcmp(trim($source[0][$i]), $strString) == 0) {
            unset($source[0][$i]);
            break;
        }
    }

    //Novo array com os dados atualizados do array anterior
    $source[0] = array_values($source[0]);
    $strNewText = implode("", $source[0]);
    
    //Pegando os descontos de acordo com o regex
    $getOff = '/.*OFF/m';
    $result = preg_match_all($getOff, $strNewText, $off);
    $lenght = count($off[0]);

    //Deletar a ultima linha dos descontos
    for($i=0; $i<count($source[0]); $i++) {
        if(strcmp(trim($source[0][$i]), "em") == 0) {
            for($j=0; $j<$lenght; $j++) {
                if(strcmp(trim($source[0][$i-1]), $off[0][$j]) == 0) {
                        unset($source[0][$i-1]);
                        break;
                }  
            }
        }
    }
    
    $source[0] = array_values($source[0]);
    $strNewText2 = implode("", $source[0]);

    $getEm = '/^em+\s$/m';
    $result = preg_match_all($getEm, $strNewText, $em);
    
    $value = array();
    for($i=0; $i<count($source[0]); $i++) {
        if(strcmp(trim($source[0][$i]), "em") == 0) {
            for($j=0; $j<count($em[0]); $j++) {
                $value[0][$i] = $source[0][$i-1];
                break;
            } 
        }
    }
    $value[0] = array_values($value[0]);

    $patternNome = '/^\w+.*GB|gb.*/m';
    $result = preg_match_all($patternNome, $strNewText, $productName);
    
        
    $patternShipping = '/^Frete.*/m';
    $result = preg_match_all($patternShipping, $strNewText, $shipping);

    for($i=0; $i<count($productName[0]); $i++) {
        $array[$i] = array("Product Name" => $productName[0][$i], "Value" => $value[0][$i], "Shipping" => $shipping[0][1]);
        $arrayFinal[$i] = preg_replace('/\n|\t|\r/', "", $array[$i]);
    }

    // echo "<pre>"; print_r($arrayFinal); echo "</pre>" 

    if(http_response_code(200)) {
        echo json_encode($arrayFinal, JSON_UNESCAPED_UNICODE);
    } 
?>
