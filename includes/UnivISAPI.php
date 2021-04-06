<?php

namespace RRZE\UnivIS;

defined('ABSPATH') || exit;

if (!function_exists('__')){
    function __($txt, $domain){
        return $txt;
    }
}


class UnivISAPI {

    protected $api;
    protected $orgID;


    public function __construct($api, $orgID) {
        $this->setAPI($api);
        $this->orgID = $orgID;
    }


    private function setAPI($api){
        // make sure we use https://DOMAIN/prg?search= no matter what input was made
        $this->api = preg_replace('/^((http|https):\/\/)?([^?\/]*)([\/?]*)/i', 'https://$3/prg?show=json&search=', $api, 1);
    }


    private static function log(string $method, string $logType = 'error', string $msg = ''){
        // uses plugin rrze-log
        $pre = __NAMESPACE__ . ' ' . $method . '() : ';
        if ($logType == 'DB'){
            global $wpdb;
            do_action('rrze.log.error', $pre . '$wpdb->last_result= ' . json_encode($wpdb->last_result) . '| $wpdb->last_query= ' . json_encode($wpdb->last_query . '| $wpdb->last_error= ' . json_encode($wpdb->last_error)));
        }else{
            do_action('rrze.log.' . $logType, __NAMESPACE__ . ' ' . $method . '() : ' . $msg);
        }
    }


    public function getData($dataType, $ID = NULL, $sort = NULL){
        $url = $this->getUrl($dataType) . $ID;
        $data = file_get_contents($url);
        if ( !$data ){
            UnivIS::log('getData', 'error', "no data returned using $url");
        }
        $data = json_decode( $data, true);

        return $this->mapIt($dataType, $data, $sort);
    }


    private function getUrl($dataType){
        $url = $this->api;
        switch($dataType){
            case 'personByID':
                $url .= 'persons&id=';
                break;
            case 'personByName':
                $url .= 'persons&fullname=';
                break;
            case 'personAll':
                $url .= 'departments&number=' . $this->orgID;
                break;
            case 'personByOrga':
            case 'personByOrgaPhonebook':
                $url .= 'persons&department=' . $this->orgID;
                break;
            case 'publicationByAuthorID':
                $url .= 'publications&authorid=';
                break;  
            case 'publicationByAuthor':
                $url .= 'publications&author=';
                break;  
            case 'publicationByDepartment':
                $url .= 'publications&department=' . $this->orgID;
                break;  
            case 'lectureByID':
                $url .= 'lectures&id=';
                break;              
            case 'lectureByDepartment':
                $url .= 'lectures&department=' . $this->orgID;
                break;   
            case 'lectureByName':
                $url .= 'lectures&lecturer=';
                break;   
            case 'jobByID':
                $url .= 'positions&closed=1&id=';
                break;
            case 'jobAll':
                $url .= 'positions&closed=1&department=' . $this->orgID;
                break;
            case 'roomByID':
                $url .= 'rooms&id=';
                break;
            case 'roomByName':
                $url .= 'rooms&name=';
                break;
            default:
                UnivIS::log('getUrl', 'error', 'unknown dataType ' . $dataType);
        }
        return $url;
    }


    public function mapIt($dataType, &$data, $sort){
        $ret = [];

        $map = [
            'personByID' => [
                'node' => 'Person',
                'fields' => [
                    'person_id' => 'id',
                    'key' => 'key',
                    'title' => 'title',
                    'atitle' => 'atitle',
                    'firstname' => 'firstname',
                    'lastname' => 'lastname',
                    'work' => 'work',
                    'department' => 'orgname',
                    'organization' => ['orgunit', 1], 
                    'email' => ['location', 'email'],
                    'phone' => ['location', 'tel'],
                    'fax' => ['location', 'fax'],
                    'street' => ['location', 'street'],
                    'city' => ['location', 'ort'],
                    'office' => ['location', 'office'],
                    'url' => ['location', 'url'],
                ],
            ],
            'publicationByAuthorID' => [
                'node' => 'Pub',
                'fields' => [
                    'publication_id' => 'id',
                    'journal' => 'journal',
                    'pubtitle' => 'pubtitle',
                    'year' => 'year',
                    // 'person_key' => ['author', 'pkey'],
                    'author' => 'author',
                    'publication_type' => 'type',
                    'hstype' => 'hstype',
                ],
            ],
            'lectureByID' => [
                'node' => 'Lecture',
                'fields' => [
                    'lecture_id' => 'id',
                    'name' => 'name',
                    'comment' => 'comment',
                    'leclanguage' => 'leclanguage',
                    'room' => ['term', 'room'],
                    'course_keys' => 'course', 
                    'lecture_type' => 'type',
                    'keywords' => 'keywords',
                    'maxturnout' => 'maxturnout',
                    'url_description' => 'url_description',
                    'schein' => 'schein',
                    'sws' => 'sws',
                    'ects' => 'ects',
                    'ects_cred' => 'ects_cred',
                    'beginners' => 'beginners',
                    'gast' => 'gast',
                    'evaluation' => 'evaluation',
                    'doz' => 'doz',
                ],
            ],
            'courses' => [
                'node' => 'Lecture',
                'fields' => [
                    'term' => 'term',
                    'coursename' => 'coursename',
                    'course_key' => 'key',
                ],
            ],
            'jobByID' => [
                'node' => 'Position',
                'fields' => [
                    'job_id' => 'id',
                    'application_end' => 'enddate',
                    'application_link' => 'desc6',
                    'job_intern' => 'intern',
                    'job_title' => 'title',
                    'job_start' => 'start',
                    'job_limitation' => 'type1',
                    'job_limitation_duration' => 'befristet',
                    'job_limitation_reason' => 'type3',
                    'job_salary_from' => 'vonbesold',
                    'job_salary_to' => 'bisbesold',
                    'job_qualifications' => 'desc2',
                    'job_qualifications_nth' => 'desc3',
                    'job_employmenttype' => 'type2',
                    'job_workhours' => 'wstunden',
                    'job_category' => 'group',
                    'job_description' => 'desc1',
                    'job_description_introduction' => 'desc5',
                    'job_experience' => 'desc2',
                    'job_benefits' => 'desc4',
                    'person_key' => 'acontact',
                ],
            ],
            'roomByID' => [
                'node' => 'Room',
                'fields' => [
                    'room_id' => 'id',
                    'key' => 'key',
                    'name' => 'name',
                    'short' => 'short',
                    'roomno' => 'roomno',
                    'buildno' => 'buildno',
                    'north' => 'north',
                    'east' => 'east',
                    'address' => 'address',
                    'size' => 'size',
                    'description' => 'description',
                    'blackboard' => 'tafel',
                    'flipchart' => 'flip',
                    'beamer' => 'beam',
                    'microphone' => 'mic',
                    'audio' => 'audio',
                    'overheadprojector' => 'ohead',
                    'tv' => 'tv',
                    'internet' => 'inet',
                ],
            ],
            'orga' => [
                'node' => 'Org',
                'fields' => [
                    'orga_positions' => 'job',
                ],
            ],
        ];



        $map['personAll'] = $map['personByID'];
        $map['personByOrga'] = $map['personByID'];
        $map['personByOrgaPhonebook'] = $map['personByID'];
        $map['personByName'] = $map['personByID'];
        $map['publicationByDepartment'] = $map['publicationByAuthorID'];
        $map['publicationByAuthor'] = $map['publicationByAuthorID'];
        $map['lectureByDepartment'] = $map['lectureByID'];
        $map['lectureByName'] = $map['lectureByID'];
        $map['jobAll'] = $map['jobByID'];
        $map['roomByName'] = $map['roomByID'];

        if (isset($data[$map[$dataType]['node']])){
            foreach($data[$map[$dataType]['node']] as $nr => $entry){
                foreach($map[$dataType]['fields'] as $k => $v){
                    if (is_array($v)){
                        if (is_int($v[1])){
                            if (isset($data[$map[$dataType]['node']][$nr][$v[0]][$v[1]])){
                                $ret[$nr][$k] = $data[$map[$dataType]['node']][$nr][$v[0]][$v[1]];
                            }elseif(isset($data[$map[$dataType]['node']][$nr][$v[0]][0])){
                                $ret[$nr][$k] = $data[$map[$dataType]['node']][$nr][$v[0]][0];
                            }
                        }else{
                            $y = 0;
                            while(isset($data[$map[$dataType]['node']][$nr][$v[0]][$y][$v[1]])){
                                $ret[$nr][$k] = $data[$map[$dataType]['node']][$nr][$v[0]][$y][$v[1]];
                                $y++;
                            }
                        }
                    }else{
                        if (isset($data[$map[$dataType]['node']][$nr][$v])){
                            $ret[$nr][$k] = $data[$map[$dataType]['node']][$nr][$v];
                        }
                    }
                }
            }
        }

        switch($dataType){
            case 'jobByID':
            case 'jobAll':
                // add person details
                $persons = $this->mapIt('personByID', $data, $sort);
                foreach($ret as $e_nr => $entry){
                    foreach($persons as $person){
                        if (isset($entry['person_key']) && $entry['person_key'] == $person['key']){
                            unset($person['person_id']);
                            $ret[$e_nr] = array_merge_recursive($entry, $person);
                            unset($ret[$e_nr]['person_key']);
                            unset($ret[$e_nr]['key']);
                        }
                    }
                }
                break;
            case 'publicationByAuthorID':
            case 'publicationByAuthor':
            case 'publicationByDepartment':
                // add person details
                $persons = $this->mapIt('personByID', $data, $sort);
                foreach($ret as $e_nr => $entry){
                    foreach($entry['author'] as $details){
                        foreach($persons as $p_nr => $person){
                            if ($person['key'] == $details['pkey']){
                                unset($person['key']);
                                $ret[$e_nr]['authors'][] = $person;
                                unset($person[$p_nr]);
                            }
                        }
                    }
                    unset($ret[$e_nr]['author']);
                }
                break;                        
            case 'lectureByID':
            case 'lectureByName':
            case 'lectureByDepartment':
                // add course details
                $courses = $this->mapIt('courses', $data, $sort);
                foreach($ret as $e_nr => $entry){
                    if (isset($entry['course_keys'])){
                        foreach($entry['course_keys'] as $course_key){
                            foreach($courses as $course){
                                if (($course['course_key'] == 'Lecture.' . $course_key) && (isset($course['term']))){
                                    unset($course['course_key']);
                                    $ret[$e_nr]['courses'][] = $course;
                                }
                            }
                        }
                        unset($ret[$e_nr]['course_keys']);
                    }
                }

                // add person details
                $persons = $this->mapIt('personByID', $data, $sort);
                foreach($ret as $e_nr => $entry){
                    foreach($entry['doz'] as $doz_key){
                        foreach($persons as $p_nr => $person){
                            if ($person['key'] == 'Person.' . $doz_key){
                                unset($person['key']);
                                $ret[$e_nr]['lecturers'][] = $person;
                                unset($person[$p_nr]);
                            }
                        }
                    }
                    unset($ret[$e_nr]['doz']);
                }
                // add room details
                $rooms = $this->mapIt('roomByID', $data, $sort);
                foreach($ret as $nr => $entry){
                    foreach($rooms as $room){
                        if (isset($entry['room']) && $entry['room'] == $room['key']){
                            $ret[$nr] = array_merge_recursive($entry, $room);
                            unset($ret[$nr]['room']);
                            unset($ret[$nr]['key']);
                        }
                    }
                }
                break;
            case 'personAll':
                // add orga details
                $orga = $this->mapIt('orga', $data, $sort);
                $orga_positions = $orga[0]['orga_positions'];
                foreach($ret as $e_nr => $entry){
                    foreach($orga_positions as $orga_position => $vals){
                        if (isset($vals['per'])){
                            foreach($vals['per'] as $person_key){
                                if (isset($entry['key']) && $entry['key'] == 'Person.' . $person_key){
                                    if (isset($ret[$e_nr]['orga_position'])){
                                        $cnt = count($ret);
                                        $ret[$cnt] = $ret[$e_nr];
                                    }else{
                                        $cnt = $e_nr;
                                    }
                                    $ret[$cnt]['orga_position'] = $vals['description'];
                                    $ret[$cnt]['orga_position_order'] = $vals['joborder'];
                                }
                            }
                        }
                    }
                }
                break;
            }

        $ret = $this->dict($ret);

        // sort
        if ($sort && in_array($dataType, ['personByID', 'personAll', 'personByOrga', 'personByName', 'personByOrgaPhonebook'])){
            usort($ret, [$this, 'sortByLastname']);            
        }

        // group by department
        if (in_array($dataType, ['personByOrga'])){
            $ret = $this->groupBy($ret, 'department');
        }

        // group by lastname's first letter
        if (in_array($dataType, ['personByOrgaPhonebook'])){
            foreach($ret as $nr => $entry){
                $ret[$nr]['letter'] = strtoupper(substr($entry['lastname'], 0, 1));
            }
            $ret = $this->groupBy($ret, 'letter');
        }
        
        // group by lecture_type_long
        if (in_array($dataType, ['lectureByID', 'lectureByName', 'lectureByDepartment'])){
            $ret = $this->groupBy($ret, 'lecture_type_long');
        }

        // sort desc and group by year
        if (in_array($dataType, ['publicationByAuthorID', 'publicationByAuthor', 'publicationByDepartment'])){
            usort($ret, [$this, 'sortByYear']);            
            $ret = $this->groupBy($ret, 'year');
        }

        // sort orga_position_order and group by orga_position
        if (in_array($dataType, ['personAll'])){
            usort($ret, [$this, 'sortByPositionorder']);            
            $ret = $this->groupBy($ret, 'orga_position');
        }

        return $ret;
    }

    private function groupBy($arr, $key) {
        $ret = [];
        foreach($arr as $val) {
            $ret[$val[$key]][] = $val;
        }
        return $ret;
    }

    private function sortByLastname($a, $b){
        return strcasecmp($a["lastname"], $b["lastname"]);
    }

    private function sortByYear($a, $b){
        return strcasecmp($b["year"], $a["year"]);
    }

    private function sortByPositionorder($a, $b){
        return strnatcmp($a["orga_position_order"], $b["orga_position_order"]);
    }

    private function dict($data){
        $fields = [
            'title' => [
                "Dr." => __('Doktor', 'rrze-univis'),
                "Prof." => __('Professor', 'rrze-univis'),
                "Dipl." => __('Diplom', 'rrze-univis'),
                "Inf." => __('Informatik', 'rrze-univis'),
                "Wi." => __('Wirtschaftsinformatik', 'rrze-univis'),
                "Ma." => __('Mathematik', 'rrze-univis'),
                "Ing." => __('Ingenieurwissenschaft', 'rrze-univis'),
                "B.A." => __('Bakkalaureus', 'rrze-univis'),
                "M.A." => __('Magister Artium', 'rrze-univis'),
                "phil." => __('Geisteswissenschaft', 'rrze-univis'),
                "pol." => __('Politikwissenschaft', 'rrze-univis'),
                "nat." => __('Naturwissenschaft', 'rrze-univis'),
                "soc." => __('Sozialwissenschaft', 'rrze-univis'),
                "techn." => __('technische Wissenschaften', 'rrze-univis'),
                "vet.med." => __('Tiermedizin', 'rrze-univis'),
                "med.dent." => __('Zahnmedizin', 'rrze-univis'),
                "h.c." => __('ehrenhalber', 'rrze-univis'),
                "med." => __('Medizin', 'rrze-univis'),
                "jur." => __('Recht', 'rrze-univis'),
                "rer." => ""
            ],
            'lecture_type' => [
                "awa" => __('Anleitung zu wiss. Arbeiten (AWA)', 'rrze-univis'),
                "ku" => __('Kurs (KU)', 'rrze-univis'),
                "ak" => __('Aufbaukurs (AK)', 'rrze-univis'),
                "ex" => __('Exkursion (EX)', 'rrze-univis'),
                "gk" => __('Grundkurs (GK)', 'rrze-univis'),
                "sem" => __('Seminar (SEM)', 'rrze-univis'),
                "es" => __('Examensseminar (ES)', 'rrze-univis'),
                "ts" => __('Theorieseminar (TS)', 'rrze-univis'),
                "ag" => __('Arbeitsgemeinschaft (AG)', 'rrze-univis'),
                "mas" => __('Masterseminar (MAS)', 'rrze-univis'),
                "gs" => __('Grundseminar (GS)', 'rrze-univis'),
                "us" => __('Übungsseminar (US)', 'rrze-univis'),
                "as" => __('Aufbauseminar (AS)', 'rrze-univis'),
                "hs" => __('Hauptseminar (HS)', 'rrze-univis'),
                "re" => __('Repetitorium (RE)', 'rrze-univis'),
                "kk" => __('Klausurenkurs (KK)', 'rrze-univis'),
                "klv" => __('Klinische Visite (KLV)', 'rrze-univis'),
                "ko" => __('Kolloquium (KO)', 'rrze-univis'),
                "ks" => __('Kombiseminar (KS)', 'rrze-univis'),
                "ek" => __('Einführungskurs (EK)', 'rrze-univis'),
                "ms" => __('Mittelseminar (MS)', 'rrze-univis'),
                "os" => __('Oberseminar (OS)', 'rrze-univis'),
                "pr" => __('Praktikum (PR)', 'rrze-univis'),
                "prs" => __('Praxisseminar (PRS)', 'rrze-univis'),
                "pjs" => __('Projektseminar (PJS)', 'rrze-univis'),
                "ps" => __('Proseminar (PS)', 'rrze-univis'),
                "sl" => __('Sonstige Lehrveranstaltung (SL)', 'rrze-univis'),
                "tut" => __('Tutorium (TUT)', 'rrze-univis'),
                "v-ue" => __('Vorlesung mit Übung (V/UE)', 'rrze-univis'),
                "ue" => __('Übung (UE)', 'rrze-univis'),
                "vorl" => __('Vorlesung (VORL)', 'rrze-univis'),
                "hvl" => __('Hauptvorlesung (HVL)', 'rrze-univis'),
                "pf" => __('Prüfung (PF)', 'rrze-univis'),
                "gsz" => __('Gremiensitzung (GSZ)', 'rrze-univis'),
                "ppu" => __('Propädeutische Übung (PPU)', 'rrze-univis'),
                "his" => __('Sprachhistorisches Seminar (HIS)', 'rrze-univis'),
            ],
            'repeat' => [
                "w1" => "",
                "w2" => __('Jede zweite Woche', 'rrze-univis'),
                "w3" => __('Jede dritte Woche', 'rrze-univis'),
                "w4" => __('Jede vierte Woche', 'rrze-univis'),
                "s1" => __('Einzeltermin am', 'rrze-univis'),
                "bd" => __('Blocktermin', 'rrze-univis'),
                '0' => __(' Sonntag', 'rrze-univis'),
                '1' => __(' Montag', 'rrze-univis'),
                '2' => __(' Dienstag', 'rrze-univis'),
                '3' => __(' Mittwoch', 'rrze-univis'),
                '4' => __(' Donnerstag', 'rrze-univis'),
                '5' => __(' Freitag', 'rrze-univis'),
                '6' => __(' Samstag', 'rrze-univis'),
                '7' => __(' Sonntag', 'rrze-univis'),
            ],                                        
            'publication_type' => [
                "artmono" => __('Artikel im Sammelband', 'rrze-univis'),
                "arttagu" => __('Artikel im Tagungsband', 'rrze-univis'),
                "artzeit" => __('Artikel in Zeitschrift', 'rrze-univis'),
                "techrep" => __('Interner Bericht (Technischer Bericht, Forschungsbericht)', 'rrze-univis'),
                "hschri" => __('Hochschulschrift (Dissertation, Habilitationsschrift, Diplomarbeit etc.)', 'rrze-univis'),
                "dissvg" => __('Hochschulschrift (auch im Verlag erschienen)', 'rrze-univis'),
                "monogr" => __('Monographie', 'rrze-univis'),
                "tagband" => __('Tagungsband (nicht im Verlag erschienen)', 'rrze-univis'),
                "schutzr" => __('Schutzrecht', 'rrze-univis'),
            ],
            'hstype' => [
                "diss" => __('Dissertation', 'rrze-univis'),
                "dipl" => __('Diplomarbeit', 'rrze-univis'),
                "mag" => __('Magisterarbeit', 'rrze-univis'),
                "stud" => __('Studienarbeit', 'rrze-univis'),
                "habil" => __('Habilitationsschrift', 'rrze-univis'),
                "masth" => __('Masterarbeit', 'rrze-univis'),
                "bacth" => __('Bachelorarbeit', 'rrze-univis'),
                "intber" => __('Interner Bericht', 'rrze-univis'),
                "diskus" => __('Diskussionspapier', 'rrze-univis'),
                "discus" => __('Discussion paper', 'rrze-univis'),
                "forber" => __('Forschungsbericht', 'rrze-univis'),
                "absber" => __('Abschlussbericht', 'rrze-univis'),
                "patschri" => __('Patentschrift', 'rrze-univis'),
                "offenleg" => __('Offenlegungsschrift', 'rrze-univis'),
                "patanmel" => __('Patentanmeldung', 'rrze-univis'),
                "gebrmust" => __('Gebrauchsmuster', 'rrze-univis'),
            ],
            'leclanguage' => [
                "D" => __('Unterrichtssprache Deutsch', 'rrze-univis'),
            ],
            'sws' => __(' SWS', 'rrze-univis'),
            'schein' => __('Schein', 'rrze-univis'),
            'ects' => __('ECTS-Studium', 'rrze-univis'),
            'ects_cred' => __('ECTS-Credits: ', 'rrze-univis'),
            'beginners' => __('für Anfänger geeignet', 'rrze-univis'),
            'gast' => __('für Gasthörer zugelassen', 'rrze-univis'),
            'evaluation' => __('Evaluation', 'rrze-univis'),
        ];

        $i = 0;

        foreach($data as $row){
            foreach($fields as $field => $values){
                if ($field == 'repeat'){
                    if (isset($data[$i]['courses'])){
                        foreach($data[$i]['courses'] as $c_nr => $course){
                            foreach($course['term'] as $m_nr => $meeting){
                                $data[$i]['courses'][$c_nr]['term'][$m_nr]['repeat'] = $values[$data[$i]['courses'][$c_nr]['term'][$m_nr]['repeat']];
                            }
                        }
                    }
                }elseif (isset($data[$i][$field])){
                    if (in_array($field, ['title'])){ // 'repeat'
                        // multi replace
                        $data[$i][$field . '_long'] = str_replace(array_keys($values), array_values($values), $data[$i][$field]);
                    }else{
                        if (!is_array($values)){
                            if ($field == 'sws'){
                                $data[$i][$field] .= $values; 
                            }elseif($field == 'ects_cred'){
                                $data[$i][$field] = $values . $data[$i][$field];
                            }else{
                                $data[$i][$field] = $values;
                            }
                        }else{
                            if (isset($values[$row[$field]])){
                                $data[$i][$field . '_long'] = $values[$row[$field]];
                            }
                            if ($field == 'lecture_type'){
                                $data[$i][$field . '_short'] = trim(substr($values[$row[$field]], 0, strpos($values[$row[$field]], '(')));
                            }
                        }
                    }
                }
            }
            $i++;
        }

        return $data;
    }

}

