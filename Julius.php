<?php
$JULIUS_HOME = "/home/h-saito/juius/.";
$JULIUS_EXEC = "julius -C ./julius-4.4.2.1/dictation-kit-v4.4/main.jconf -C ./julius-4.4.2.1/dictation-kit-v4.4/am-gmm.jconf -input fil
e";
$SERVER_PORT = 8000;
$ASR_FILEPATH = '/home/h-saito/julius/';
$ASR_IN = 'Sample.wav';
$ASR_RESULT = 'Sample.out';
$OUT_CHIKUM = 5;



        $descriptorspec = array(
                                0 => array("pipe","r"),//stdin
                                1 => array("pipe","w"),//stdout
                                2 => array("file","/home/h-saito/julius/error-output.txt","a"),//stderr ファイルに書き込み
                                 );

        $cwd = $JULIUS_HOME;
        $env = array(null); //??

        $p = proc_open($JULIUS_EXEC,$descriptorspec,$pipes,$cwd,$env);

        if(is_resource($p)){

                fwrite($pipes[0],$ASR_IN);
                fclose($pipes[0]);

                echo stream_get_contents($pipes[1]);
                fclose($pipes[1]);

                $return_value = proc_close($p);

                echo "command returned $return_value\n";

?>
