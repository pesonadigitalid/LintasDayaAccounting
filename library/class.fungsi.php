<?php

/**
 * Author : Yogi Pratama | Mail me [at] youputra@gmail.com
 * Description :
 * Licence to
 * @copyright 2013.
 */

class Fungsi
{
    protected $db;

    function __construct()
    {
        $this->db = new ezSQL_mysql(YGDBUSER, YGDBPASS, YGDBNAME, YGDBHOST);
    }

    function antiSQLInjection($input)
    {
        $reg = "/(delete|update|union|insert|'|;|javascript|script|exec)/";
        return (preg_replace($reg, "", $input));
    }

    function encodeURL($data)
    {
        return substr(md5($data), 0, 10);
    }

    function registerMenu($menu)
    {
    }

    function createLink($var)
    {
        return str_replace(" ", "-", strtolower($var));
    }

    function generateOutletKey($id)
    {
        do {
            $firstdigitsn = $id;
            $rand = substr(number_format(time() * rand(), 0, '', ''), 0, 5);
            $rand = $firstdigitsn . $rand;

            $query = $this->db->get_var("SELECT * FROM bm_outlet WHERE outlet_key='" . $rand . "'");
        } while ($query > 0);
        return $rand;
    }

    function convertCurrency($val)
    {
        return "Rp. " . number_format($val, "0", "", ".") . ",-";
    }

    function convertCurrencyNotifikasi($val)
    {
        if ($val > 999999)
            return "Rp. " . number_format(substr($val, 0, -3), "0", "", ".") . "K";
        else
            return "Rp. " . number_format($val, "0", "", ".") . "";
    }

    function convertCurrency2($val)
    {
        return number_format($val, "0", "", ".");
    }

    function get_real_ip()
    {
        if (isset($_SERVER["HTTP_CLIENT_IP"])) {
            return $_SERVER["HTTP_CLIENT_IP"];
        } elseif (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            return $_SERVER["HTTP_X_FORWARDED_FOR"];
        } elseif (isset($_SERVER["HTTP_X_FORWARDED"])) {
            return $_SERVER["HTTP_X_FORWARDED"];
        } elseif (isset($_SERVER["HTTP_FORWARDED_FOR"])) {
            return $_SERVER["HTTP_FORWARDED_FOR"];
        } elseif (isset($_SERVER["HTTP_FORWARDED"])) {
            return $_SERVER["HTTP_FORWARDED"];
        } else {
            return $_SERVER["REMOTE_ADDR"];
        }
    }

    function getExtension($str)
    {
        $i = strrpos($str, ".");
        if (!$i) {
            return "";
        }
        $l = strlen($str) - $i;
        $ext = substr($str, $i + 1, $l);
        return $ext;
    }

    function uploadImage($image, $image_loc, $destination)
    {
        $filename = stripslashes($image);
        $extension = $this->getExtension($filename);
        $extension = strtolower($extension);

        $extract = explode(".", $filename);
        $filename = $extract[0];

        if (($extension == "jpg") || ($extension == "jpeg") || ($extension == "png") || ($extension == "gif")) {
            $image_name = $filename . '.' . $extension;
            $newname = $destination . $image_name;
            $copied = copy($image_loc, $newname);
        }

        return $image_name;
    }

    function uploadFile($image, $image_loc, $destination)
    {
        $filename = stripslashes($image);
        $extension = $this->getExtension($filename);
        $extension = strtolower($extension);

        $extract = explode(".", $filename);
        $filename = $extract[0];

        $image_name = $filename . '.' . $extension;
        $newname = $destination . $image_name;
        $copied = copy($image_loc, $newname);

        return $image_name;
    }

    function uploadImage2($image, $destination, $width_size = "", $file_name = "")
    {
        $handle = new Upload($image);
        if ($handle->uploaded) {
            if ($file_name != "") {
                $handle->file_new_name_body = $file_name;
            }

            if ($width_size != "") {
                $handle->image_resize = true;
                $handle->image_x = $width_size;
                $handle->image_ratio_y = true;
            }

            $handle->allowed = array('image/*');

            $handle->Process($destination);

            if ($handle->processed) {
                $lastfile = $handle->file_dst_name;
                $handle->Clean();
                return $lastfile;
            } else {
                $handle->Clean();
                return null;
            }
        }
    }

    function discardImage($filename, $location = "public/files/images/")
    {
        if (file_exists($location . $filename))
            unlink($location . $filename);
    }

    function wordLimiter($text, $limit = 160, $chars = '0123456789')
    {
        if (strlen($text) > $limit) {
            $words = str_word_count($text, 2, $chars);
            $words = array_reverse($words, TRUE);
            foreach ($words as $length => $word) {
                if ($length + strlen($word) >= $limit) {
                    array_shift($words);
                } else {
                    break;
                }
            }
            $words = array_reverse($words);
            $text = implode(" ", $words);
        }
        return $text;
    }

    function titleCase($words, $charList = null)
    {
        // Use ucwords if no delimiters are given

        $words = strtolower($words);
        if (!isset($charList)) {
            $charList = " ";
        }

        // Go through all characters
        $capitalizeNext = true;

        for ($i = 0, $max = strlen($words); $i < $max; $i++) {
            if (strpos($charList, $words[$i]) !== false) {
                $capitalizeNext = true;
            } else if ($capitalizeNext) {
                $capitalizeNext = false;
                $words[$i] = strtoupper($words[$i]);
            }
        }

        return $words;
    }


    /* draws a calendar */
    function draw_calendar($month, $year, $event = array())
    {

        /* draw table */
        $calendar = '<table cellpadding="0" cellspacing="0" class="calendar">';
        $calendar .= '<tr class="calendar-row"><td class="month-name calendar-day-head" colspan="7">' . $this->changeMonthNameID($month) . " $year" . '</td></tr>';
        /* table headings */
        $headings = array('Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu');
        $calendar .= '<tr class="calendar-row"><td class="calendar-day-head">' . implode('</td><td class="calendar-day-head">', $headings) . '</td></tr>';

        /* days and weeks vars now ... */
        $running_day = date('w', mktime(0, 0, 0, $month, 1, $year));
        $days_in_month = date('t', mktime(0, 0, 0, $month, 1, $year));
        $days_in_this_week = 1;
        $day_counter = 0;
        $dates_array = array();

        /* row for week one */
        $calendar .= '<tr class="calendar-row">';

        /* print "blank" days until the first of the current week */
        for ($x = 0; $x < $running_day; $x++) :
            $calendar .= '<td class="calendar-day-np"> </td>';
            $days_in_this_week++;
        endfor;

        /* keep going with days.... */
        for ($list_day = 1; $list_day <= $days_in_month; $list_day++) :
            if (in_array($list_day, $event))
                $calendar .= '<td class="calendar-day event-day">';
            else
                $calendar .= '<td class="calendar-day">';
            /* add in the day number */
            $calendar .= '<div class="day-number">' . $list_day . '</div>';

            /** QUERY THE DATABASE FOR AN ENTRY FOR THIS DAY !!  IF MATCHES FOUND, PRINT THEM !! **/
            $calendar .= str_repeat('<p> </p>', 2);

            $calendar .= '</td>';
            if ($running_day == 6) :
                $calendar .= '</tr>';
                if (($day_counter + 1) != $days_in_month) :
                    $calendar .= '<tr class="calendar-row">';
                endif;
                $running_day = -1;
                $days_in_this_week = 0;
            endif;
            $days_in_this_week++;
            $running_day++;
            $day_counter++;
        endfor;

        /* finish the rest of the days in the week */
        if ($days_in_this_week < 8) :
            for ($x = 1; $x <= (8 - $days_in_this_week); $x++) :
                $calendar .= '<td class="calendar-day-np"> </td>';
            endfor;
        endif;

        /* final row */
        $calendar .= '</tr>';

        /* end the table */
        $calendar .= '</table>';

        /* all done, return result */
        return $calendar;
    }

    function changeMonthNameID($val)
    {
        switch ($val) {
            case "1":
                $return = "Januari";
                break;
            case "2":
                $return = "Februari";
                break;
            case "3":
                $return = "Maret";
                break;
            case "4":
                $return = "April";
                break;
            case "5":
                $return = "Mei";
                break;
            case "6":
                $return = "Juni";
                break;
            case "7":
                $return = "Juli";
                break;
            case "8":
                $return = "Agustus";
                break;
            case "9":
                $return = "September";
                break;
            case "10":
                $return = "Oktober";
                break;
            case "11":
                $return = "November";
                break;
            case "12":
                $return = "Desember";
                break;
        }

        switch ($val) {
            case "January":
                $return = "Januari";
                break;
            case "February":
                $return = "Februari";
                break;
            case "March":
                $return = "Maret";
                break;
            case "April":
                $return = "April";
                break;
            case "Mey":
                $return = "Mei";
                break;
            case "June":
                $return = "Juni";
                break;
            case "July":
                $return = "Juli";
                break;
            case "August":
                $return = "Agustus";
                break;
            case "September":
                $return = "September";
                break;
            case "October":
                $return = "Oktober";
                break;
            case "November":
                $return = "November";
                break;
            case "December":
                $return = "Desember";
                break;
        }

        return $return;
    }

    function changeDayNameID($val)
    {
        switch ($val) {
            case "Sunday":
                $return = "Minggu";
                break;
            case "Monday":
                $return = "Senin";
                break;
            case "Tuesday":
                $return = "Selasa";
                break;
            case "Wednesday":
                $return = "Rabu";
                break;
            case "Thursday":
                $return = "Kamis";
                break;
            case "Friday":
                $return = "Jumat";
                break;
            case "Saturday":
                $return = "Sabtu";
                break;
        }

        return $return;
    }

    function changeDateID($val)
    {
        $exp = explode(",", $val);
        $return = $this->changeDayNameID($exp[0]) . ", ";

        $exp2 = explode(" ", trim($exp[1]));
        $return .= $exp2[1] . " " . $this->changeMonthNameID($exp2[0]) . " " . $exp2[2];

        return $return;
    }

    function changeDateID2($val)
    {
        $exp = explode(" ", trim($val));
        $return = $exp[1] . " " . $this->changeMonthNameID($exp[0]) . " " . $exp[2];

        return $return;
    }

    function getFirstParagraph($string)
    {
        //$string = htmlspecialchars($string);
        //var_dump($string);
        $string = substr($string, 0, strpos($string, "</p>") + 4);
        return $string;
    }

    function setIDPemasangan($string)
    {
        if ($string != "") {
            $id = substr($string, -5);
            $id = floatval($id) + 1;
        } else
            $id = 1;
        if ($id < 10)
            $id = "0000" . $id;
        else if ($id < 100)
            $id = "000" . $id;
        else if ($id < 1000)
            $id = "00" . $id;
        else if ($id < 10000)
            $id = "0" . $id;
        return date("Ym") . $id;
    }

    function ENDate($date)
    {
        $exp = explode("/", $date);
        return $exp[2] . "-" . $exp[1] . "-" . $exp[0];
    }

    function IDDate($date)
    {
        $exp = explode("-", $date);
        return $exp[2] . "/" . $exp[1] . "/" . $exp[0];
    }

    function authAccess($level, $min)
    {
        if ($level > $min)
            return header("Location:" . PRSONPATH);
    }

    function authLogin($login, $allowed_access)
    {
        if ($login != $allowed_access)
            return header("Location: " . PRSONPATH . "login");
    }

    function authAccessRekening($rek_posisi, $kode_rek)
    {
        // var_dump($_SESSION['jabatan']);
        // var_dump($_SESSION['departement']);
        // Accounting MMS
        // Accounting LD
        // Finance LD
        if ($_SESSION["jabatan"] == 2 && $_SESSION["departement"] == 4 && (
            ($rek_posisi == 'Debet' && $kode_rek == '1-1112') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1114') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1133') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1139') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1142') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1116') ||
            // ($rek_posisi == 'Debet' && $kode_rek == '1-1266') ||
            // ($rek_posisi == 'Debet' && $kode_rek == '1-1264') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1119') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1311') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1321') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1145') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1267') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1126') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1127') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1128') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1137') ||
            ($rek_posisi == 'Debet' && substr($kode_rek, 0, 4) == '1-12') ||
            ($rek_posisi == 'Debet' && substr($kode_rek, 0, 2) == '5-') ||
            ($rek_posisi == 'Debet' && substr($kode_rek, 0, 2) == '6-') ||
            ($rek_posisi == 'Debet' && substr($kode_rek, 0, 2) == '7-') ||
            ($rek_posisi == 'Debet' && substr($kode_rek, 0, 2) == '8-') ||
            $rek_posisi == 'Kredit'
        )) {
            // if ($_SESSION["jabatan"] == 2 && $_SESSION["departement"] == 4 && (
            //     ($rek_posisi == 'Debet'
            //         && substr($kode_rek, 0, 4) == '1-11'
            //         && ($kode_rek == '1-1112'
            //             || $kode_rek == '1-1114'
            //             || $kode_rek == '1-1133'
            //             || $kode_rek == '1-1139'
            //             || $kode_rek == '1-1142'
            //             || $kode_rek == '1-1116'
            //             || $kode_rek == '1-1266'
            //             || $kode_rek == '1-1264'
            //             || $kode_rek == '1-1119'))
            //     ||
            //     (($rek_posisi == 'Debet'
            //         && substr($kode_rek, 0, 4) != '1-11'))
            //     || $rek_posisi == 'Kredit')) {
            return 1;
        } else if (($_SESSION["jabatan"] == 24 || $_SESSION["jabatan"] == 2) && $_SESSION["departement"] == 27 && (
            ($rek_posisi == 'Debet' && $kode_rek == '1-1111') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1112') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1113') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1114') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1115') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1118') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1119') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1120') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1264') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1266') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1133') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1139') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1142') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1144') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1145') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1267') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1116') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1144') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1200') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1121') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1210') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1213') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1214') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1215') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1217') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1220') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1230') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1240') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1250') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1260') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1278') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1279') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1320') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1321') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1322') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1211') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1212') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1221') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1255') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1265') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1268') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1269') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1270') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1271') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1272') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1273') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1274') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1275') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1276') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1277') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1280') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1300') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1310') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1311') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1400') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1410') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1420') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1430') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1500') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1126') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1127') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1128') ||
            ($rek_posisi == 'Debet' && substr($kode_rek, 0, 5) == '1-111') ||
            ($rek_posisi == 'Debet' && substr($kode_rek, 0, 2) == '5-') ||
            ($rek_posisi == 'Debet' && substr($kode_rek, 0, 2) == '6-') ||
            ($rek_posisi == 'Debet' && substr($kode_rek, 0, 2) == '7-') ||
            ($rek_posisi == 'Debet' && substr($kode_rek, 0, 2) == '8-') ||
            $rek_posisi == 'Kredit'
        )) {
            // if ($_SESSION["jabatan"] == 2 && $_SESSION["departement"] == 4 && (
            //     ($rek_posisi == 'Debet'
            //         && substr($kode_rek, 0, 4) == '1-11'
            //         && ($kode_rek == '1-1112'
            //             || $kode_rek == '1-1114'
            //             || $kode_rek == '1-1133'
            //             || $kode_rek == '1-1139'
            //             || $kode_rek == '1-1142'
            //             || $kode_rek == '1-1116'
            //             || $kode_rek == '1-1266'
            //             || $kode_rek == '1-1264'
            //             || $kode_rek == '1-1119'))
            //     ||
            //     (($rek_posisi == 'Debet'
            //         && substr($kode_rek, 0, 4) != '1-11'))
            //     || $rek_posisi == 'Kredit')) {
            return 1;
        } else if ($_SESSION["jabatan"] == 22 && $_SESSION["departement"] != 4 && (($rek_posisi == 'Debet' && substr($kode_rek, 0, 4) == '1-11' && ($kode_rek == '1-1113' || $kode_rek == '1-1111' || $kode_rek == '1-1117' || $kode_rek == '1-1144')) || (($rek_posisi == 'Debet' && substr($kode_rek, 0, 4) != '1-11')) || $rek_posisi == 'Kredit')) {
            return 1;
        } else if ($_SESSION["jabatan"] == 24 && $_SESSION["departement"] != 4 && (
            ($rek_posisi == 'Debet' && substr($kode_rek, 0, 4) == '1-11' && ($kode_rek == '1-1137')) ||
            ($rek_posisi == 'Debet' && substr($kode_rek, 0, 5) == '1-111') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1116') ||
            ($rek_posisi == 'Debet' && $kode_rek == '1-1117') ||
            ($rek_posisi == 'Debet' && substr($kode_rek, 0, 2) == '5-') ||
            ($rek_posisi == 'Debet' && substr($kode_rek, 0, 2) == '6-') ||
            ($rek_posisi == 'Debet' && substr($kode_rek, 0, 2) == '7-') ||
            ($rek_posisi == 'Debet' && substr($kode_rek, 0, 2) == '8-') ||
            $rek_posisi == 'Kredit'
        )) {
            return 1; // JABATAN = MMS Administrasi, buka akses BCA 022 dan Petty Cash
        } else if ($_SESSION["jabatan"] == 3) {
            return 1;
        } else {
            return 0;
        }
    }
}
