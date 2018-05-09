<?
class Images
{
    public static $lang = null;
    /*
    $arg
    + src[String]				: A forrása a feltöltött fájloknak ($_FILE[?])
    + upDir[String]				: A képek mentésének helye.
    - required[0|1] 			: Kötelező a kép feltöltése, vagy sem. (1 = kötelező)
    - text[String]				: A feltöltött mező referencia neve hibaüzenetben.
    - noRoot[true|false-] 		: true -> nem sorolja be almappákba
    - makeThumbImg[true|false-] : true -> készítsen bélyegképeket
    - makeWaterMark[true|false-]: true -> készítsen vízjelet a képre
    - fileName[String]  		: true Str -> A kép neve ez lesz.
    - maxFileSize[Int] 			: Maximálisan engedélyezett fájlméret képenként.
    - imgLimit[Int]             : Max. ennyi fájlt tölt fel.

    */
    public static function upload($arg = array())
    {
        $si    = $arg[src];
        $file  = true;
        $dir   = $arg[upDir];
        $src   = $_FILES[$si];
        $noDir = false;


        self::$lang = $arg['lang'];

        if ($arg[mod] == "add") { // Kiegészítő
            $dir = $arg[tDir];
        }


        if ($dir == null || $dir == "") {
            $dir   = substr(IMG, 1) . "uploads/";
            $noDir = true;
        }

        if (!file_exists($dir)) {
            if ($arg[mod] != "add") {
                throw new \Exception(sprintf(self::$lang['lng_image_root_not_valid'], $dir));
            } else if ($arg[mod] == "add") {
                $dir   = substr(IMG, 1) . "uploads/";
                $noDir = true;
            }
        }

        if ($src[size][0] == 0) {
            if ($arg[mod] != "add") {
                if ($arg[required] == 1) {
                    throw new \Exception(sprintf(self::$lang['lng_image_notfile_uploaded'], $arg[text]));
                } else {
                    $file = false;
                }
            }
        } else {
            // Ellenőrzése
            $te        = -1;
            $errOfType = '';
            $fn        = 0;
            foreach ($src[type] as $ftt) {
                $te++;
                $fn++;
                // Fájlformátum ellenőrzés
                if (!in_array($ftt, array(
                    'image/jpg',
                    'image/png',
                    'image/jpeg'
                ))) {
                    $errOfType .= sprintf(self::$lang['lng_image_not_valid_fileformat'], 'jpg, png', $src[name][$te]);
                }

                // Fájlméret ellenőrzés - ADD: 2013/07
                if ($arg[maxFileSize]) {
                    $fileKb = ceil($src[size][$te] / 1024);
                    if ($fileKb > $arg[maxFileSize]) {
                        $errOfType .= sprintf(self::$lang['lng_image_not_allowed_filesize'], $fileKb, $arg[maxFileSize], $src[name][$te]);
                    }
                }
            }

            // Darabszám
            if ($arg[imgLimit] > 0) {
                if ($fn > $arg[imgLimit]) {
                    $errOfType .= sprintf(self::$lang['lng_image_not_allowed_filenumber'], $arg[imgLimit]);
                }
            }

            if ($errOfType != '') {
                throw new \Exception($errOfType);
            }

            // Minden szükséges adat megvan
            if ($arg[mod] != "add" || $noDir) {
                #Random mappa név
                if (!$arg[noRoot]) {
                    $dh = self::sHash(7);
                }
                # Feltötendő képek mappája
                $updir = $dir . $dh . '/';

                if (!file_exists($updir)) {
                    mkdir($updir, 0777);
                    chmod($updir, 0777);
                }
            } else {
                $updir = $dir;
            }

            $p        = 0;
            $allFiles = array();

            $step = -1;

            foreach ($src[tmp_name] as $tmp) {
                $step++;
                usleep(020000); // 0.2 mp várakozás

                $mt       = explode(" ", str_replace(".", "", microtime()));
                $hash     = self::sHash(7);
                // Fájlnév kiterjesztés nélkül
                $fls      = $hash . $mt[0];
                // Fájlnév kiterjesztéssel
                $fileName = $fls . '.jpg';

                if (isset($arg['fileName'])) {
                    $fls      = $arg[fileName] . '__' . $mt[0];
                    $fileName = $fls . "." . pathinfo($src[name][$step], PATHINFO_EXTENSION);
                }

                $fln = $fileName;

                if ($p == 0) {
                    $ffile = $fln;
                }

                move_uploaded_file($tmp, $updir . $fln);

                // Bélyegképek
                if ($arg[makeThumbImg]) {
                    self::makeThumbnail($updir . $fln, $updir, $fls, 'thb150_', 150);
                    self::makeThumbnail($updir . $fln, $updir, $fls, 'thb75_', 75);
                }

                // Vízjelezés
                if ($arg[makeWaterMark]) {
                    $kep = $updir . $fln;
                    self::makeWatermarkedImage(WATERMARK_IMG, $kep, 'középen');
                }

                $p++;
                $allFiles[] = $updir . $fln;
            }
            $file = true;
        }


        if ($file) {
            $back = array(
                "dir" => $updir,
                "file" => $updir . $ffile,
                "allUploadedFiles" => $allFiles
            );
            return $back;
        } else {
            return false;
        }
    }

    private static function sHash($n = 7)
    {
        return substr(md5(microtime()), 0, $n);
    }

    static function makeWatermarkedImage($wmk, $file, $pos)
    {
        if ($wmk != "") {
            $fln = basename($file);
            $ext = end(explode(".", $fln));

            if ($ext == "jpg") {
                // Eredeti kép
                $kep = imagecreatefromjpeg($file);
                list($kx, $ky) = getimagesize($file);

                // Vízjel
                $wm = imagecreatefrompng($wmk);
                list($wmw, $wmh) = getimagesize($wmk);
                $wmpos = $pos;

                switch ($wmpos) {
                    case 'bal-fent';
                        $x = 5;
                        $y = 5;
                        break;
                    case 'bal-lent';
                        $x = 5;
                        $y = $ky - $wmh - 5;
                        break;
                    case 'jobb-fent';
                        $x = $kx - $wmw - 5;
                        $y = 5;
                        break;
                    case 'jobb-lent';
                        $x = $kx - $wmw - 5;
                        $y = $ky - $wmh - 5;
                        break;
                    case 'középen';
                        $x = ($kx / 2) - ($wmw / 2);
                        $y = ($ky / 2) - ($wmh / 2);
                        break;
                }

                imagecopy($kep, $wm, $x, $y, 0, 0, $wmw, $wmh);
                imagejpeg($kep, $file, 100);
                imagedestroy($kep);
            }
        }
    }

    private static function makeThumbnail($src, $dir, $name, $pref, $maxWidth)
    {

        // Alap műveletek
        # Forrás fájl másolása
        copy($src, $dir . $pref . $name . '.jpg');
        # Forrás kép elérése
        $src = $dir . $pref . $name . '.jpg';
        # Virtuálos kép létrehozás
        $wi  = imagecreatefromjpeg($src);
        # Kép méreteinek beolvasása
        list($iw, $ih) = getimagesize($src);

        // Méretarányos méretcsökkentés
        $dHeight = floor($ih * ($maxWidth / $iw));

        // Kép módosító
        $vi = imagecreatetruecolor($maxWidth, $dHeight);
        imagecopyresampled($vi, $wi, 0, 0, 0, 0, $maxWidth, $dHeight, $iw, $ih);

        // Módosítások érvényesítése / Output
        imagejpeg($vi, $dir . $pref . $name . '.jpg', 85);

        // Temponális változók eltávolítása
        imagedestroy($vi);
    }

    public static function getThumbImg($type, $src)
    {
        $ct   = explode("/", $src);
        $max  = count($ct);
        $im   = $ct[$max - 1];
        $root = str_replace($im, "", $src);
        if ($im == 'noimg.png') {
            $thmb = '/' . $root . $im;
        } else {
            $thmb = '/' . $root . 'thb' . $type . '_' . $im;
        }
        $thmb = ltrim($thmb, '/');
        return $thmb;
    }

    public static function showThumbImg($src, $w = 100)
    {
        $url = '/services/image/thumbnail/' . base64_encode($src) . '/' . $w . '/';

        return $url;
    }

    public function fullImageUrl($img, $domain = '')
    {
        $img     = ($img == '') ? 'no-img.png' : $img;
        $url     = '';
        $iroot   = array(
            'ittettunk' => 'public/Ittettunk/images/',
            'holegyunk' => 'public/Hosting/images/'
        );
        $domains = array(
            'ittettunk' => DOMAIN_ITTETTUNK,
            'holegyunk' => DOMAIN_HOLEGYUNK
        );

        switch ($domain) {
            case 'ittettunk':
                if (substr($img, 0, 6) != 'public') {
                    $img = $iroot[$domain] . $img;
                }
                $img = $domains[$domain] . $img;
                break;
            case 'holegyunk':
                if (substr($img, 0, 6) != 'public') {
                    $img = $iroot[$domain] . $img;
                }
                $img = $domains[$domain] . $img;
                break;
        }
        $url = $img;
        return $url;
    }

    public static function thumbImg($img, $arg = array())
    {
        $square_size = ($arg[s]) ? $arg[s] : 100;
        $cacheTime   = 60 * 60 * 24;

        if (substr($img, 0, 4) != 'http')
            if (strpos($img, IMG) === false) {
                $img = substr(IMG, 1) . $img;
            }
        //echo $img;
        // get width and height of original image
        $imagedata       = getimagesize($img);
        $original_width  = $imagedata[0];
        $original_height = $imagedata[1];

        if ($original_width > $original_height) {
            $new_height = $square_size;
            $new_width  = $new_height * ($original_width / $original_height);
        }
        if ($original_height > $original_width) {
            $new_width  = $square_size;
            $new_height = $new_width * ($original_height / $original_width);
        }
        if ($original_height == $original_width) {
            $new_width  = $square_size;
            $new_height = $square_size;
        }

        $new_width  = round($new_width);
        $new_height = round($new_height);

        // load the image
        if (substr_count(strtolower($img), ".jpg") or substr_count(strtolower($img), ".jpeg")) {
            $original_image = imagecreatefromjpeg($img);
        }
        if (substr_count(strtolower($img), ".gif")) {
            $original_image = imagecreatefromgif($img);
        }
        if (substr_count(strtolower($img), ".png")) {
            $original_image = imagecreatefrompng($img);
        }

        $smaller_image = imagecreatetruecolor($new_width, $new_height);
        $square_image  = imagecreatetruecolor($square_size, $square_size);

        imagecopyresampled($smaller_image, $original_image, 0, 0, 0, 0, $new_width, $new_height, $original_width, $original_height);

        if ($new_width > $new_height) {
            $difference      = $new_width - $new_height;
            $half_difference = round($difference / 2);
            imagecopyresampled($square_image, $smaller_image, 0 - $half_difference + 1, 0, 0, 0, $square_size + $difference, $square_size, $new_width, $new_height);
        }
        if ($new_height > $new_width) {
            $difference      = $new_height - $new_width;
            $half_difference = round($difference / 2);
            imagecopyresampled($square_image, $smaller_image, 0, 0 - $half_difference + 1, 0, 0, $square_size, $square_size + $difference, $new_width, $new_height);
        }
        if ($new_height == $new_width) {
            imagecopyresampled($square_image, $smaller_image, 0, 0, 0, 0, $square_size, $square_size, $new_width, $new_height);
        }


        // if no destination file was given then display a png
        header('Pragma: public');
        header('Cache-Control: max-age=' . $cacheTime . ', public');
        header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + ($cacheTime)));
        header('Content-Type: image/png');


        imagepng($square_image, NULL, 9);

        // save the smaller image FILE if destination file given
        /*
        if(substr_count(strtolower($destination_file), ".jpg")){
        imagejpeg($square_image,$destination_file,100);
        }
        if(substr_count(strtolower($destination_file), ".gif")){
        imagegif($square_image,$destination_file);
        }
        if(substr_count(strtolower($destination_file), ".png")){
        imagepng($square_image,$destination_file,9);
        }
        */

        imagedestroy($original_image);
        imagedestroy($smaller_image);
        imagedestroy($square_image);
    }

    public static function full($file)
    {
        $file = str_replace(array(
            '../img/'
        ), array(
            IMG
        ), $file);
        return $file;
    }

    public static function getAllImg($path, $wh = null)
    {
        if ($path != "" && is_dir($path)) {
            $data  = new DirectoryIterator($path);
            $files = array();

            foreach ($data as $fl) {
                if ($fl->isFile()) {
                    $fln = $fl->getFilename();
                    if ($wh == null) {
                        $files[] = $path . $fln;
                    } else if ($wh != null && $wh != "big") {
                        if (strpos($fln, $wh) !== false) {
                            $files[] = $path . $fln;
                        }
                    } else {
                        if (strpos($fln, "thb75_") !== 0 && strpos($fln, "thb150_") !== 0) {
                            $files[] = $path . $fln;
                        }
                    }
                }
            }
            return $files;
        }
        return array();
    }


    public static function getParentFolderName($file)
    {

    }

    /* ADD: 2013/07 */
    public static function dellAllFolderImgs($folder)
    {
        $allImg = self::getAllImg($folder);

        foreach ($allImg as $img) {
            if (file_exists($img)) {
                unlink($img);
            }
        }
    }

    /* ADD: 2013/07 */
    public static function dellSubImgs($url)
    {
        $url  = ltrim($url, '/');
        $curl = explode('/', $url);
        $nurl = count($curl);

        $file = $curl[$nurl - 1];
        $root = str_replace($file, '', $url);

        $dellfile   = array();
        $dellfile[] = $root . $file;

        if (file_exists($root . 'thb150_' . $file))
            $dellfile[] = $root . 'thb150_' . $file;

        if (file_exists($root . 'thb75_' . $file))
            $dellfile[] = $root . 'thb75_' . $file;

        foreach ($dellfile as $df) {
            unlink($df);
        }
    }
}
?>
