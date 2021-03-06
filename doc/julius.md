メインで触ったのがJuliusです。また、Watson Speech to textも扱ったのでJuliusとAPIの比較検証をする内容になっています。  
いずれも音声ファイルを認識させています。  

まずは最低限の環境構築を行います。  
Windows10でwslでUbuntuインストールします。↓下記記事参照  
https://qiita.com/Aruneko/items/c79810b0b015bebf30bb  
※Juliusのインストール時にgitを使うのでインストールをしておく、お好みでvimも  

Ubuntuにphpをインストールします。↓下記記事参照  
https://laboradian.com/use-php72-with-ubuntu1604/  

IBMの音声認識APIのWatson Speech to textを触ります。  
詳しくは↓  
https://www.ibm.com/watson/services/speech-to-text/  

はじめに  
https://cloud.ibm.com/login　でアカウント作成を行いログインをします。  


このチュートリアルに従います。  


チュートリアルは英語の音声ファイルを使用しています。日本語の音声を認識させるためにはステップ2の赤丸部分を変更する必要があります。  

日本語のサンプル音声ファイルの準備をします。  
https://blog.apar.jp/web/9036/  
↓使用した音声ファイル  


```
$ curl -X POST -u "apikey:{API鍵}" --header "Content-Type: audio/flac" --data-binary @{path_to_file}audio-file.flac "{url}/v1/recognize?model=ja-JP_BroadbandModel"
```  
上記の赤線部分を書き換えます。  
 
Watsonの認識結果  
```

{
   "results": [
      {
         "alternatives": [
            {
               "confidence": 0.83,
               "transcript": "青森県 八戸市 で 古く から 愛 される 郷土 料理 せんべい 汁 領野 借りて 取った 獲物 具材 に した 汁物 に  ちぎった 南部 せんべい を 入れて 食べた のが せんべい 汁 の 始まり です "
            }
         ],
         "final": true
      },
      {
         "alternatives": [
            {
               "confidence": 0.94,
               "transcript": "だし汁 が たっぷり 染み込んで いる せんべい の 不思議な 食感 を 楽しめ ます "
            }
         ],
         "final": true
      }
   ],
   "result_index": 0
```  

次にPHPでWatson Speech to textを使います。  

PHPコード  
```
<?php

$file = file_get_contents('/mnt/c/Users/h-saito/Downloads/senbeijiru.flac');

$url = 'https://gateway-tok.watsonplatform.net/speech-to-text/api/v1/recognize';
$model = 'ja-JP_NarrowbandModel';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url . '?model=' . $model);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $file);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_USERPWD, 'apikey' . ':' . 'jz8S58TekitouAPI-ka');

$headers = array();
$headers[] = 'Content-Type: audio/flac';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}
curl_close($ch);
print_r($result);
?>
```  

PHP Watsonの認識結果  
```

{
   "results": [
      {
         "alternatives": [
            {
               "confidence": 0.76,
               "transcript": "青森県 八戸市 で 売る から 愛 される 郷土 料理 せんべい 汁 "
            }
         ],
         "final": true
      },
      {
         "alternatives": [
            {
               "confidence": 0.62,
               "transcript": "良也 借りて とった ええ もの ござい ました 汁物 に "
            }
         ],
         "final": true
      },
      {
         "alternatives": [
            {
               "confidence": 0.66,
               "transcript": "一 二 あ 南武線 で を 入れて 食べた のが せんべい 汁 の 始まり で す "
            }
         ],
         "final": true
      },
      {
         "alternatives": [
            {
               "confidence": 0.7,
               "transcript": "だし汁 が たっぷり 染み込んで いる 生命 の 不思議な 食感 を 楽しめ ます "
            }
         ],
         "final": true
      }
   ],
   "result_index": 0
```  
先ほどと同じ音声ファイルを使用しました。  


次にJuliusを使いました。  
UbuntuにJuliusをインストールし、認識させます。  
https://qiita.com/ekzemplaro/items/dcfd51c24f2c3a020c7b  
※julius音声ファイルはchannel1でサンプルレート16000である必要があります。  
ディレクトリをdictationkitに移動し実行する。またはパスを指定する。  
 ```julius -C main.jconf -C am-gmm.jconf -input rawfile```  

使用した音声ファイル  
https://github.com/sayonari/julius_post  

```$ wget http://sayonari.com/data/test_16000.wav```  
をdictationkitのディレクトリでコマンドをたたきます。  
※flacだと動かないです。  
wgetがなかったら```$ sudo apt install wget```をしてください。  


Julius 認識結果  
```
pass1_best:  これ は マイク の テイスト です 。
pass1_best_wordseq: <s> これ+代名詞 は+助詞 マイク+名詞 の+助詞 テイスト+名詞 です+助動詞 </s>
pass1_best_phonemeseq: silB | k o r e | w a | m a i k u | n o | t e: s u t o | d e s u | silE
pass1_best_score: -7954.535156
### Recognition: 2nd pass (RL heuristic best-first)
STAT: 00 _default: 33344 generated, 3122 pushed, 388 nodes popped in 328
sentence1:  これ は マイク の テスト です 。
wseq1: <s> これ+代名詞 は+助詞 マイク+名詞 の+助詞 テスト+名詞 です+助動詞 </s>
phseq1: silB | k o r e | w a | m a i k u | n o | t e s u t o | d e s u | silE
cmscore1: 0.517 0.687 0.481 0.570 0.323 0.187 0.734 1.000
score1: -7960.165039
```  

これでJuliusが動きました。  

次にphpでJuliusを動かします。  
検索してたらPythonでやるスクリプトがあったのでこれをもとにPHPにかきかえました。  
↓使用した音声ファイル。  

Pythonスクリプト  
```
#!/usr/bin/env python
# -*- coding: utf-8 -*-
# Julius web server (with CherryPy:http://www.cherrypy.org/)
# written by Ryota NISHIMURA 2015/Dec./16

### configure ###########
JULIUS_HOME		= "/home/nishimura/Software/julius/."
JULIUS_EXEC		= "./julius -C ./julius-kit/am-gmm.jconf -C ./julius-kit/main.jconf -input file -outfile"
SERVER_PORT 	= 8000
ASR_FILEPATH	= '/home/nishimura/Public/asr/'
ASR_IN			= 'ch_asr.wav'
ASR_RESULT		= 'ch_asr.out'
OUT_CHKNUM		= 5 # for avoid that the output file is empty

### import ##############
import cherrypy
import subprocess
import sys
import os
import time
import socket
from cherrypy import request

### class define ########
class ASRServer(object):
	# Julius execution -> subprocess
	p = subprocess.Popen (JULIUS_EXEC, shell=True, cwd=JULIUS_HOME, 
		stdin=subprocess.PIPE,stdout=subprocess.PIPE, stderr=subprocess.STDOUT,
		close_fds=True)
	(stdouterr, stdin) = (p.stdout, p.stdin)

	# main task
	def index(self):
		return """
			<html><body>
				<h2>Julius Server</h2>
				USAGE:<br />
				- 16000Hz, wav(or raw)-file, big-endian, mono<br />
				<br />
				<form action="asr_julius" method="post" enctype="multipart/form-data">
				filename: <input type="file" name="myFile" /><br />
				<input type="submit" />
				</form>
			</body></html>
			"""
	index.exposed = True
		
	def asr_julius(self, myFile):	        
		# receive WAV file from client & write WAV file
		with open(ASR_FILEPATH + ASR_IN, 'wb') as f:
			f.write(myFile.file.read())
		f.close()
		
		# ASR using Julius
		if os.path.exists(ASR_FILEPATH + ASR_RESULT):
			os.remove(ASR_FILEPATH + ASR_RESULT)			# delete a previous result file
		self.p.stdin.write(ASR_FILEPATH + ASR_IN + '\n')	# send wav file name to Julius
		self.p.stdin.flush()

		# wait for result file creation & result writing (avoid the file empty)
		while not (os.path.exists(ASR_FILEPATH + ASR_RESULT) and len(open(ASR_FILEPATH + ASR_RESULT).readlines()) == OUT_CHKNUM):
			time.sleep(0.1)
			
		# read result file & send it to client
		outlines = open(ASR_FILEPATH + ASR_RESULT).read()
		outlines = "<xmp>" + outlines + "</xmp>"
		return outlines
	asr_julius.exposed = True

### main ################
# get own IP
s = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
s.connect(("8.8.8.8", 80))
server_ip = s.getsockname()[0]

# start the CherryPy server
```  
後半はPHPに書き換えるときに関係ないです。

phpで書き換え  
```
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
```  
PHP Julius 認識結果  
```
STAT: 267568 samples (16.72 sec.)
STAT: ### speech analysis (waveform -> MFCC)
### Recognition: 1st pass (LR beam)
pass1_best:  Ｓ は 古来 の い た が 決まっ て ま 、 細かい 、 はい 、 健康 きか ない の ね 、 外科 など を どう なる もの の 電源 が 起き 、 また 評価 の ない 録音 さ せ 、 おい 、 気 に 頭 の ご 協力 葉 や 、 。
pass1_best_wordseq: <s> Ｓ+記号 は+助詞 古来+名詞 の+助詞 い+動詞 た+助動詞 が+助詞 決まっ+動詞 て+助詞 ま+感動詞 、+補助記号 細かい+形容詞 、+補助記号 はい+感動詞 、+補助記号 健康+名詞 きか+動詞 ない+助動詞 の+助詞 ね+助詞 、+補助記号 外科+名詞 など+助詞 を+助詞 どう+ 副詞 なる+動詞 もの+名詞 の+助詞 電源+名詞 が+助詞 起き+動詞 、+補助記号 また+接続詞 評価+名詞 の+助詞 ない+形容詞 録音+名詞 さ+動詞 せ+助動詞 、+補助記号 おい+感動詞 、+補助記号 気+名詞 に+助詞 頭+名詞 の+助詞 ご+接頭辞 協力+名詞 葉+名詞 や+助詞 、+補助記号 </s>
pass1_best_phonemeseq: silB | e s u | w a | k o r a i | n o | i | t a | g a | k i m a q | t e | m a | sp | k o m a k a i | sp | h a i | sp | k e N k o: | k i k a | n a i | n o | n e | sp | g e k a | n a d o | o | d o: | n a r u | m o n o | n o | d e N g e N | g a | o k i | sp | m a t a | hy o: k a | n o | n a i | r o k u o N | s a | s e | sp | o i | sp | k i | n i | a t a m a | n o | g o | ky o: ry o k u | h a | y a | sp | silE
pass1_best_score: -53316.585938
### Recognition: 2nd pass (RL heuristic best-first)
STAT: 00 _default: 715548 generated, 28051 pushed, 4128 nodes popped in 1670
sentence1:  Ｓ は 古来 茶 を いただき まし て な 、 まだ 、 はい 、 健康 、 五 年 、 時 に 、 何 の 音 の 出る もの の 電源 が 起き 、 また 、 巨額 の ない 録音 、 さて 、 五 人 旅客 機 に 釜 の 公 教育 、 四 、 Ｓ 。
wseq1: <s> Ｓ+記号 は+助詞 古来+名詞 茶+名詞 を+助詞 いただき+動詞 まし+助動詞 て+助詞 な+助詞 、+補助記号 まだ+副詞 、+補助記号 はい+ 感動詞 、+補助記号 健康+名詞 、+補助記号 五+名詞 年+名詞 、+補助記号 時+名詞 に+助詞 、+補助記号 何+代名詞 の+助詞 音+名詞 の+助詞 出る+動詞 もの+名詞 の+助詞 電源+名詞 が+助詞 起き+動詞 、+補助記号 また+接続詞 、+補助記号 巨額+形状詞 の+助詞 ない+形容詞 録音+名詞 、+補助記号 さて+接続詞 、+補助記号 五+名詞 人+接尾辞 旅客+名詞 機+名詞 に+助詞 釜+名詞 の+助詞 公+名詞 教育+名詞 、+補助記号 四+名詞 、+補 助記号 Ｓ+記号 </s>
phseq1: silB | e s u | w a | k o r a i | ch a | o | i t a d a k i | m a sh i | t e | n a | sp | m a d a | sp | h a i | sp | k e N k o: | sp | g o | n e N | sp | j i | n i | sp | n a N | n o | o t o | n o | d e r u | m o n o | n o | d e N g e N | g a | o k i | sp | m a t a | sp | ky o g a k u | n o | n a i | r o k u o N | sp | s a t e | sp | g o | n i N | ry o k a k u | k i | n i | k a m a | n o | k o: | ky o: i k u | sp | y o N | sp | e s u | silE
cmscore1: 1.000 0.211 0.304 0.093 0.072 0.277 0.728 0.210 0.241 0.049 0.237 0.007 0.207 0.072 0.688 0.108 0.072 0.274 0.024 0.937 0.093 0.017 0.642 0.266 0.321 0.370 0.355 0.035 0.329 0.904 0.168 0.883 0.069 0.419 0.573 0.075 0.065 0.425 0.165 0.599 0.012 0.042 0.732 0.362 0.017 0.384 0.034 0.057 0.066 0.559 0.022 0.203 0.035 0.337 0.594 0.011 1.000
score1: -53826.011719
```  

音響モデルをGMMからDNNに変えました。  
DNNとは  
DNN (Deep Neural Network)のシステムでは高精度な音響モデルを使用します。そのため処理が重くなり，手順も複雑になりますが，GMM版よりも認識精度が向上します。  
GMMからDNNへの変え方  
GMM  
```
$JULIUS_EXEC = "julius -C ./julius-4.4.2.1/dictation-kit-v4.4/main.jconf -C ./julius-4.4.2.1/dictation-kit-v4.4/am-gmm.jconf -input file"; 
```  

DNN  
```
$JULIUS_EXEC = "julius -C ./julius-4.4.2.1/dictation-kit-v4.4/main.jconf -C ./julius-4.4.2.1/dictation-kit-v4.4/am-dnn.jconf -dnnconf .
/julius-4.4.2.1/dictation-kit-v4.4/julius.dnnconf -input file";
```  


DNN　認識結果  
```
STAT: 267568 samples (16.72 sec.)
STAT: ### speech analysis (waveform -> MFCC)
### Recognition: 1st pass (LR beam)
pass1_best:  本日 は 古来 の 頂 まして 山 が 、 海外 に 先立ち まし て 顧客 様 に お 願い を さ れ ます 携帯 電話 など の で ある もの の 電源 を 切り 下さい まだ 評価 の ない 録音 さ れ なく 遠慮 ください 皆 様 の ご 協力 を よろしく お 気 が 。
pass1_best_wordseq: <s> 本日+名詞 は+助詞 古来+名詞 の+助詞 頂+名詞 まして+副詞 山+名詞 が+助詞 、+補助記号 海外+名詞 に+助詞 先立ち+動詞 まし+助動詞 て+助詞 顧客+名詞 様+接尾辞 に+助詞 お+接頭辞 願い+名詞 を+助詞 さ+動詞 れ+助動詞 ます+助動詞 携帯+名詞 電話+名詞 など+ 助詞 の+助詞 で+助動詞 ある+動詞 もの+名詞 の+助詞 電源+名詞 を+助詞 切り+動詞 下さい+動詞 まだ+副詞 評価+名詞 の+助詞 ない+形容詞 録音+名詞 さ+動詞 れ+助動詞 なく+助動詞 遠慮+名詞 ください+動詞 皆+名詞 様+接尾辞 の+助詞 ご+接頭辞 協力+名詞 を+助詞 よろしく+副詞 お+接頭辞 気+名詞 が+助詞 </s>
pass1_best_phonemeseq: sp_S | h_B o_I N_I j_I i_I ts_I u_E | w_B a_E | k_B o_I r_I a_I i_E | n_B o_E | i_B t_I a_I d_I a_I k_I i_E | m_B a_I sh_I i_I t_I e_E | y_B a_I m_I a_E | g_B a_E | sp_S | k_B a_I i_I g_I a_I i_E | n_B i_E | s_B a_I k_I i_I d_I a_I ch_I i_E | m_B a_I sh_I i_E | t_B e_E | k_B o_I ky_I a_I k_I u_E | s_B a_I m_I a_E | n_B i_E | o_S | n_B e_I g_I a_I i_E | o_S | s_B a_E | r_B e_E | m_B a_I s_I u_E | k_B e:_I t_I a_I i_E | d_B e_I N_I w_I a_E | n_B a_I d_I o_E | n_B o_E | d_B e_E | a_B r_I u_E | m_B o_I n_I o_E | n_B o_E | d_B e_I N_I g_I e_I N_E | o_S | k_B i_I r_I i_E | k_B u_I d_I a_I s_I a_I i_E | m_B a_I d_I a_E | hy_B o:_I k_I a_E | n_B o_E | n_B a_I i_E | r_B o_I k_I u_I o_I N_E | s_B a_E | r_B e_E | n_B a_I k_I u_E | e_B N_I ry_I o_E | k_B u_I d_I a_I s_I a_I i_E | m_B i_I n_I a_E | s_B a_I m_I a_E | n_B o_E | g_B o_E | ky_B o:_I ry_I o_I k_I u_E | o_S | y_B o_I r_I o_I sh_I i_I k_I u_E | o_S | k_B i_E | g_B a_E | sp_S
pass1_best_score: 763.649902
### Recognition: 2nd pass (RL heuristic best-first)
WARNING: 00 _default: hypothesis stack exhausted, terminate search now
STAT: 00 _default: 12 sentences have been found
STAT: 00 _default: 189280 generated, 16581 pushed, 2334 nodes popped in 1660
sentence1:  本日 は ご 来場 いただき まして 巻 の 中 が 退学 会議 に 先立ち まし て 、 お 客 様 に お 願い も し て あげ ます 携帯 電話 など 音 の 出る もの の 電源 を 切り ください マナー 許可 の ない 録音 撮影 は ご 遠慮 ください 皆 様 の ご 協力 を よろしく 笑 。
wseq1: <s> 本日+名詞 は+助詞 ご+接頭辞 来場+名詞 いただき+動詞 まして+副詞 巻+名詞 の+助詞 中+名詞 が+助詞 退学+名詞 会議+名詞 に+助詞 先立ち+動詞 まし+助動詞 て+助詞 、+補助記号 お+接頭辞 客+名詞 様+接尾辞 に+助詞 お+接頭辞 願い+名詞 も+助詞 し+動詞 て+助詞 あげ+動詞  ます+助動詞 携帯+名詞 電話+名詞 など+助詞 音+名詞 の+助詞 出る+動詞 もの+名詞 の+助詞 電源+名詞 を+助詞 切り+動詞 ください+動詞 マナー+名詞 許可+名詞 の+助詞 ない+形容詞 録音+名詞 撮影+名詞 は+助詞 ご+接頭辞 遠慮+名詞 ください+動詞 皆+名詞 様+接尾辞 の+助詞 ご+接頭辞 協力+名詞 を+助詞 よろしく+副詞 笑+名詞 </s>
phseq1: sp_S | h_B o_I N_I j_I i_I ts_I u_E | w_B a_E | g_B o_E | r_B a_I i_I j_I o:_E | i_B t_I a_I d_I a_I k_I i_E | m_B a_I sh_I i_I t_I e_E | m_B a_I k_I i_E | n_B o_E | n_B a_I k_I a_E | g_B a_E | t_B a_I i_I g_I a_I k_I u_E | k_B a_I i_I g_I i_E | n_B i_E | s_B a_I k_I i_I d_I a_I ch_I i_E | m_B a_I sh_I i_E | t_B e_E | sp_S | o_S | ky_B a_I k_I u_E | s_B a_I m_I a_E | n_B i_E | o_S | n_B e_I g_I a_I i_E | m_B o_E | sh_B i_E | t_B e_E | a_B g_I e_E | m_B a_I s_I u_E | k_B e:_I t_I a_I i_E | d_B e_I N_I w_I a_E | n_B a_I d_I o_E | o_B t_I o_E | n_B o_E | d_B e_I r_I u_E | m_B o_I n_I o_E | n_B o_E | d_B e_I N_I g_I e_I N_E | o_S | k_B i_I r_I i_E | k_B u_I d_I a_I s_I a_I i_E | m_B a_I n_I a:_E | ky_B o_I k_I a_E | n_B o_E | n_B a_I i_E | r_B o_I k_I u_I o_I N_E | s_B a_I ts_I u_I e:_E | w_B a_E | g_B o_E | e_B N_I ry_I o_E | k_B u_I d_I a_I s_I a_I i_E | m_B i_I n_I a_E | s_B a_I m_I a_E | n_B o_E | g_B o_E | ky_B o:_I ry_I o_I k_I u_E | o_S | y_B o_I r_I o_I sh_I i_I k_I u_E | w_B a_I r_I a_I i_E | sp_S
cmscore1: 1.000 0.924 0.529 0.982 0.881 0.188 0.775 0.009 0.854 0.037 0.170 0.017 0.543 0.813 0.991 0.774 0.689 0.741 0.668 0.946 0.590 0.156 0.938 0.743 0.096 0.961 0.018 0.089 0.355 0.990 0.009 0.790 0.880 0.403 0.179 0.485 0.965 0.992 0.522 0.109 0.535 0.030 0.475 0.956 0.310 0.815 0.775 0.934 0.908 0.998 0.330 0.942 0.509 0.889 0.758 0.894 0.123 0.765 0.007 1.000
score1: 955.145691
```  
体感15～20秒ほどGMMより実行に時間かかりますが、認識精度は高くなることを確認できました。  

同じ音声ファイルを使い、Watsonでもやってみました。  

Watson 認識結果  
```
{
   "results": [
      {
         "alternatives": [
            {
               "confidence": 0.95,
               "transcript": "本日 は ご来場 いただき まして まことに ありがとう ございます "
            }
         ],
         "final": true
      },
      {
         "alternatives": [
            {
               "confidence": 0.81,
               "transcript": "開演 に 先立ち まして お客様 に お願い 申し上げ ます "
            }
         ],
         "final": true
      },
      {
         "alternatives": [
            {
               "confidence": 0.96,
               "transcript": "携帯 電話 など 音 の 出る もの の 電源 を お 切り 下さい "
            }
         ],
         "final": true
      },
      {
         "alternatives": [
            {
               "confidence": 0.85,

```  
Juliusよりも認識精度が高いことがわかります。  

WatsonとJuliusの認識結果比較  
Watsonの方がJuliusよりも認識精度高いことがわかりました。  
素の状態でも結構イケる。単語単体で発話よりも文章にするとより認識精度が上がる。(まずは単語単体を認識してから文章で補正するからみたい)
つまりまとめると、言語モデルは音声を文字へ変換する際にどの単語や例文が近いかを判断するための材料(ボキャブラリー)を拡充するもの、テキストなので理解しやすくデータ量を増すほどよさそう。音響モデルは音源の波形から単語を認識する波長や振幅や間隔をチューニングするもの、音声認識の仕組の知識がないとチューニングが難しいかもしれませんね。  
長い文章だとAPIの良さがでた。
Juliusは短文に強い。
