<?php
/********************
	二维码生成
********************/
namespace app\index\controller;

class QRcode
{
    
    /**
     * 生成指定网址的二维码
     * @param string $url 二维码中所代表的网址
     * @param string $label 标签参数
     */
    public function create_qrcode($url,$label)
    {
        $qrCode = new \Endroid\QrCode\QrCode();//创建生成二维码对象
        $qrCode->setText($url)
        ->setSize(150)
        ->setPadding(10)
        ->setErrorCorrection('high')
        ->setForegroundColor(array('r' => 0, 'g' => 0, 'b' => 0, 'a' => 0))
        ->setBackgroundColor(array('r' => 255, 'g' => 255, 'b' => 255, 'a' => 0))
        ->setLabel($label)      //标签
        ->setLabelFontSize(10)  //标签中字体的大小
        ->setImageType(\Endroid\QrCode\QrCode::IMAGE_TYPE_PNG);
        header("Content-type: image/png");
        $qrCode->render();
    }
    //使用方法
	//在模板文件中使用<img src="{:url('index/qrcode/create_qrcode')}">
}
    