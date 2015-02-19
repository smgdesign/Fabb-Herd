<?php

/* 
 * Copyright SMG Design 2014
 * <richard.wilson@smgdesign.org>
 */

namespace templates;
class cattle {
    static function BuildOutput() {
        $tbl = array('c'=>'tbl_cattle');
        $cols = array(
            'c'=>array('*'),
            'p'=>array('url', 'alt')
        );
        $joins = array(array('table'=>'tbl_cattle_photos', 'as'=>'p', 'on'=>array('p.cattle_id', '=', 'c.id')));
        $cond = array();
        $limit = array("ORDER BY c.name ASC");
        $data = \data\collection::buildQuery("SELECT", $tbl, $joins, $cols, $cond, $limit);
        $items = array("links"=>array(), "details"=>array());
        if ($data[1] > 0) {
            $cattle = array();
            foreach ($data[0] as $photo) {
                if (!isset($cattle[$photo['id']])) {
                    $cattle[$photo['id']] = array('id'=>$photo['id'], 'name'=>$photo['name'], 'dob'=>$photo['dob'], 'category'=>$photo['category'], 'height'=>$photo['height'], 'description'=>$photo['description'], 'icon'=>$photo['icon'], 'sire'=>$photo['sire'], 'dam'=>$photo['dam'], 'video'=>$photo['video'], 'link'=>$photo['link'], 'photos'=>array());
                }
                if (!empty($photo['url'])) {
                    $cattle[$photo['id']]['photos'][] = array('url'=>$photo['url'], 'alt'=>$photo['alt']);
                }
            }
        }
        foreach ($cattle as $i=>$item) {
            $items['links'][] = \templates\cattle::CattleTitle($i, $item);
            $items['details'][] = \templates\cattle::CattleDescription($item);
        }
        $outp = '<ul id="cattle-list">';
        $outp .= implode('', $items['links']);
        $outp .= '</ul>';
        $outp .= implode('', $items['details']);
        return $outp;
    }
    static function CattleTitle($i, $item) {
        return '<li id="icon-'.$item['id'].'"><span class="holder"><img '.((empty($item['icon'])) ? 'src="/img/cattle/default.png" class="default"' : 'src="'.$item['icon'].'"').' alt="'.$item['name'].'" /></span><span class="text">'.$item['name'].'</span></li>';
    }
    static function CattleDescription($item) {
        global $common;
        $images = array();
        foreach ($item['photos'] as $photo) {
            $images[] = '<li><img src="'.$photo['url'].'" alt="'.$photo['alt'].'" /></li>';
        }
        $dob = new \DateTime($item['dob']);
        $tod = new \DateTime();
        $age = $dob->diff($tod)->y;
        return '<div class="cattle-info" id="cattle-'.$item['id'].'">
                    <div class="cattle-details">
                        <h2 class="cattle-name">'.$item['name'].'</h2>
                        <!--<div class="cattle-figures">
                            '.(($item['dob'] !== '0000-00-00') ? '<div class="cattle-figure"><span>'.$age.'</span></div>' : '').'
                            '.((!empty($item['category'])) ? '<div class="cattle-figure"><span>'.$item['category'].'</span></div>' : '').'
                            '.((!empty($item['gender'])) ? '<div class="cattle-figure"><span>'.$item['gender'].'</span></div>' : '').'
                            '.((!empty($item['height'])) ? '<div class="cattle-figure"><span>'.$item['height'].'</span></div>' : '').'
                        </div>-->
                        <div class="cattle-description">
                            '.$item['description'].'
                            '.(($item['dob'] !== '0000-00-00') ? '<br /><strong>DOB: </strong>'.$dob->format('d/m/Y') : '').'
                            '.((!empty($item['sire'])) ? '<br /><strong>Sire: </strong>'.$item['sire'] : '').'
                            '.((!empty($item['dam'])) ? '<br /><strong>Dam: </strong>'.$item['dam'] : '').'
                            '.((!empty($item['link'])) ? '<br /><a href="'.$item['link'].'" target="_blank">More Information</a>' : '').'
                        </div>
                    </div>
                    <div class="cattle-images">
                        <div class="image-view">
                            <div class="img-container"></div>
                        </div>
                        <ul class="image-thumbs">
                            '.implode('', $images).'
                            '.(!empty($item['video']) ? '<li video-url="'.htmlentities($item['video']).'" class="cattle-video"><img src="/img/play-icon.gif" /></li>' : '').'
                        </ul>
                    </div>
                </div>';
    }
}