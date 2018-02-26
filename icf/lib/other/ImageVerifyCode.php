<?php
/**
 *============================
 * author:Farmer
 * time:2017/12/27
 * blog:blog.icodef.com
 * function:生成一个图片验证码
 *============================
 */


namespace icf\lib\other;


class ImageVerifyCode {

    private $im;
    private $left;
    private $bgc;
    private $imageL = 150;
    private $imageH = 50;
    private $fontSize = 26;
    private $code = '';

    public function create() {
        $this->left = mt_rand(10, 15);
        $this->backcolor();//生成背景颜色
        $this->code = '';
        for ($i = 0; $i < 4; $i++) {//绘制字符
            $tmp = self::getRandString(1);
            $this->createWord($tmp);
            $this->code .= $tmp;
        }
        $this->_writeCurve();//绘制干扰线
        return $this->code;
    }

    /**
     * 取随机字符串
     * @author Farmer
     * @param $length
     * @param $type
     * @return string
     */
    public static function getRandString($length, $type = 2) {
        $randString = '123456789qwwertyuopasdfghjkzxcvbnmQWERTYUIPASDFGHHJKLZXCVBNM';
        $retStr = '';
        for ($n = 0; $n < $length; $n++) {
            $retStr .= substr($randString, mt_rand(0, 9 + $type * 24), 1);
        }
        return $retStr;
    }

    public function getImage() {
        return $this->im;
    }

    public function display() {
        $this->create();
        header('Pragma: no-cache');
        header('Content-type: image/png');
        imagepng($this->im);
        imagedestroy($this->im);
        return $this->code;
    }

    private function backcolor() {
        $this->im = imagecreatetruecolor($this->imageL, $this->imageH);
        $this->bgc = imagecolorallocate($this->im, 255, 255, 255);
        imagefill($this->im, 0, 0, imagecolorallocate($this->im, 255, 255, 255));
    }

    private function createWord($word) {
        $font = __ROOT_ . '/icf/res/arial.ttf';
        $size = mt_rand(24, 28);
        imagefttext($this->im, $size, mt_rand(-60, 60), $this->left, $size * 1.5, $this->createRandColor(), $font, $word);
        $this->left += mt_rand($size * 1.2, $size * 1.6);
    }

    private function createRandColor() {
        return imagecolorallocate($this->im, mt_rand(10, 200), mt_rand(10, 200), mt_rand(10, 200));
    }

    /**
     * 算法来自:http://www.piaoyi.org/php/php-yanzhengma-rand-shape.html
     * 画一条由两条连在一起构成的随机正弦函数曲线作干扰线(你可以改成更帅的曲线函数)
     *      正弦型函数解析式：y=Asin(ωx+φ)+b
     *      各常数值对函数图像的影响：
     *        A：决定峰值（即纵向拉伸压缩的倍数）
     *        b：表示波形在Y轴的位置关系或纵向移动距离（上加下减）
     *        φ：决定波形与X轴位置关系或横向移动距离（左加右减）
     *        ω：决定周期（最小正周期T=2π/∣ω∣）
     */
    protected function _writeCurve() {
        $A = mt_rand(1, $this->imageH / 2);                  // 振幅
        $b = mt_rand(-$this->imageH / 4, $this->imageH / 4);   // Y轴方向偏移量
        $f = mt_rand(-$this->imageH / 4, $this->imageH / 4);   // X轴方向偏移量
        $T = mt_rand($this->imageH * 1.5, $this->imageL * 2);  // 周期
        $w = (2 * M_PI) / $T;

        $px1 = 0;  // 曲线横坐标起始位置
        $px2 = mt_rand($this->imageL / 2, $this->imageL * 0.667);  // 曲线横坐标结束位置
        $t_bg = $this->createRandColor();
        for ($px = $px1; $px <= $px2; $px = $px + 0.9) {
            if ($w != 0) {
                $py = $A * sin($w * $px + $f) + $b + $this->imageH / 2;  // y = Asin(ωx+φ) + b
                $i = (int)(($this->fontSize - 6) / 4);
                while ($i > 0) {
                    imagesetpixel($this->im, $px + $i, $py + $i, $t_bg);
                    //这里画像素点比imagettftext和imagestring性能要好很多
                    $i--;
                }
            }
        }

        $A = mt_rand(1, $this->imageH / 2);                  // 振幅
        $f = mt_rand(-$this->imageH / 4, $this->imageH / 4);   // X轴方向偏移量
        $T = mt_rand($this->imageH * 1.5, $this->imageL * 2);  // 周期
        $w = (2 * M_PI) / $T;
        $b = $py - $A * sin($w * $px + $f) - $this->imageH / 2;
        $px1 = $px2;
        $px2 = $this->imageL;
        for ($px = $px1; $px <= $px2; $px = $px + 0.9) {
            if ($w != 0) {
                $py = $A * sin($w * $px + $f) + $b + $this->imageH / 2;  // y = Asin(ωx+φ) + b
                $i = (int)(($this->fontSize - 8) / 4);
                while ($i > 0) {
                    imagesetpixel($this->im, $px + $i, $py + $i, $t_bg);
                    //这里(while)循环画像素点比imagettftext和imagestring用字体大小一次画出
                    //的（不用while循环）性能要好很多
                    $i--;
                }
            }
        }
    }

}