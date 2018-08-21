<?php

function getMglDate($date_time)
{
    $year = substr($date_time, 0, 4);
    $month = substr($date_time, 5, 2);
    $day = substr($date_time, 8, 2);

    return (int)$month. ' сарын '.$day.', '.$year;
}

function imageSrc($object, $width, $height)
{
    $src = $object->getImageSource();

    $image = Image::open($src)->scaleResize($width, $height, 'transparent');
    $image = $image->png();

    return '/'.$image;
}

// image tag
function imageTag($object, $width, $height, $options = array())
{
    $src = $object->getImageSource();

    $image = Image::open($src)->scaleResize($width, $height, 'transparent');
    $image = $image->png();

    return image_tag('/'.$image, array_merge(array(), $options));
}

// бараа
function productImageTag($product, $width, $height, $options = array())
{
    $src = $product->getImageSource();

    $image = Image::open($src)->scaleResize($width, $height, 'transparent');
    $image = $image->png();

    return image_tag('/'.$image, array_merge(array(), $options));
}

// бараа хайлтын үед гаргах зураг
function productSearchImageTag($product, $width, $height, $options = array())
{
    $src = $product['image_source'];

    $image = Image::open($src)->scaleResize($width, $height, 'transparent');
    $image = $image->png();

    return image_tag('/'.$image, array_merge(array('title' => $product['name'], 'alt' => $product['name']), $options));
}

// брэндийн зураг
function brandImageTag($brand, $width, $height, $options = array())
{
    $src = $brand->getLogoSource();

    $image = Image::open($src)->scaleResize($width, $height, 'transparent');
    $image = $image->png();

    return image_tag('/'.$image, array_merge(array('title' => $brand['name'], 'alt' => $brand['name']), $options));
}

// агуулгын зураг
function contentImageTag($content, $width, $height, $options = array())
{
    $src = $content->getImageSource();

    $image = Image::open($src)->scaleResize($width, $height, 'transparent');
    $image = $image->png();

    return image_tag('/'.$image, array_merge(array('title' => $content['title'], 'alt' => $content['title']), $options));
}

// барааны ангиллын зураг
function productCategoryImageTag($category, $width, $height, $options = array())
{
    $src = $category->getImageSource();

    $image = Image::open($src)->scaleResize($width, $height, 'transparent');
    $image = $image->png();

    return image_tag('/'.$image, array_merge(array('title' => $category['name'], 'alt' => $category['name']), $options));
}

/**
 * Үнэ бодох
 *
 * @author EagLe
 * 
 * @param double $price
 * @param char $prefix
 * @param double $value
 * 
 * @return double $price
 */

/**
 * Үнэ харуулах
 *
 * @author EagLe
 * 
 * @param double $price
 * @param char $symbol
 * 
 * @return string $price
 */
function price_format($price, $symbol = '₮') {
    $price = (float) $price;

    return number_format($price, 0, '', ' ');
}

/**
 * Нэгжийн сериал дугаарыг харуулах
 *
 * @param string $serial
 * 
 * @return string $serial
 */
function serial_format($serial) {
    $str = $serial;

    if (strlen($serial) == 12) {
        $str = substr($serial, 0, 4) . ' ' . substr($serial, 4, 4) . ' ' . substr($serial, 8, 4);
    }

    return $str;
}

function pagination($pager, $uri)
{
    $navigation = '';
    if ($pager->haveToPaginate()) {
        $navigation = '<div class="pagination-panel"><ul class="pagination pagination-sm man">';
        $uri .= (preg_match('/\?/', $uri) ? '&' : '?') . 'page=';

        // First and previous page
        if ($pager->getPage() != 1) {
            $navigation .= '<li>' . link_to('&laquo;', $uri . $pager->getPreviousPage()) . '</li>';
        } else {
            $navigation .= '<li><a href="javascript:void(0)">&laquo;</a></li>';
        }

        // Pages one by one
        foreach ($pager->getLinks() as $page) {
            if ($page == $pager->getPage()) {
                $navigation .= '<li class="active"><a href="javascript:void(0)">' . $page . '</a></li>';
            } else {
                $navigation .= '<li>' . link_to($page, $uri . $page) . '</li>';
            }
        }

        // Next and last page
        if ($pager->getPage() != $pager->getCurrentMaxLink()) {
            $navigation .= '<li>' . link_to('&raquo;', $uri . $pager->getNextPage()) . '</li>';
        } else {
            $navigation .= '<li><a href="javascript:void()">&raquo;</a></li>';
        }
        $navigation .= '</ul></div>';
    }

    return $navigation;
}
function pager_search($total_page, $page, $uri)
{
    $navigation = '<ul class="pagination">';

    if ($total_page > 1):

        $start_page = ($page - 2 > 0 ) ? $page - 2 : 1;
        $end_page = ($page + 2 < $total_page) ? $page + 2 : $total_page;

        $uri .= (preg_match('/\?/', $uri) ? '&' : '?').'page=';

        $navigation .= '<ul class="pagination">';
        if ($page != 1)
        {
           // $navigation .= '<li>' . link_to('&laquo;', $uri . $pager->getPreviousPage()) . '</li>';
            $navigation .= '<li><a href="'.$uri.($page - 1).'">&laquo;</a></li>';
        }
        else
        {
            $navigation .= '<li><a class="disabled" href="javascript:;">&laquo;</a></li>';
        }

        for ($p = $start_page; $p <= $end_page; $p++)
        {
            if ($p == $page)
            {
                $navigation .= '<li class="active"><a href="javascript:;">'.$p.'</a></li>';
            }
            else
            {
                $navigation .= '<li><a href="'.$uri.$p.'">'.$p.'</a></li>';
            }
        }

        if ($page != $total_page)
        {
            $navigation .= '<li><a href="'.$uri.($page + 1).'">&raquo;</a></li>';
        }
        else
        {
            $navigation .= '<li><a class="disabled" href="javascript:;">&raquo;</a></li>';
        }

    endif;
    $navigation .= '</ul>';

    return $navigation;
}

function pagination_v2($pager, $uri)
{
    $navigation = '';
    if ($pager->haveToPaginate()) {
        $navigation = '<ul class="pagination">';
        $uri .= (preg_match('/\?/', $uri) ? '&' : '?') . 'page=';

        // First and previous page
        if ($pager->getPage() != 1) {
           // $navigation .= '<li><a href="'.$uri . $pager->getPreviousPage().'"><span class="glyphicon glyphicon-chevron-left"></span></a></li>';
            $navigation .= '<li>' . link_to('&laquo;', $uri . $pager->getPreviousPage()) . '</li>';
        } else {
            $navigation .= '<li><a href="javascript:;">&laquo;</a></li>';
        }

        // Pages one by one
        foreach ($pager->getLinks() as $page) {
            if ($page == $pager->getPage()) {
                $navigation .= '<li class="active"><a href="javascript:;">' . $page . '</a></li>';
            } else {
                $navigation .= '<li>' . link_to($page, $uri . $page) . '</li>';
            }
        }

        // Next and last page
        if ($pager->getPage() != $pager->getCurrentMaxLink()) {
           // $navigation .= '<li><a href="'.$uri . $pager->getNextPage().'"><span class="glyphicon glyphicon-chevron-right"></span></a></li>';
            $navigation .= '<li>' . link_to('&raquo;', $uri . $pager->getNextPage()) . '</li>';
        } else {
            $navigation .= '<li><a href="javascript:;">&raquo;</a></li>';
        }
        $navigation .= '</ul>';
    }

    return $navigation;
}

function time_ago($from_time, $to_time = null, $include_seconds = false) {
    if (!is_numeric($from_time)) {
        $from_time = strtotime($from_time);
    }

    $to_time = $to_time ? $to_time : time();

    $distance_in_minutes = floor(abs($to_time - $from_time) / 60);
    $distance_in_seconds = floor(abs($to_time - $from_time));

    $string = '';
    $parameters = array();

    if ($distance_in_minutes <= 1) {
        if (!$include_seconds) {
            $string = $distance_in_minutes == 0 ? 'саяхан' : '1 минут өмнө';
        } else {
            if ($distance_in_seconds <= 5) {
                $string = '5 секундийн өмнө';
            } else if ($distance_in_seconds >= 6 && $distance_in_seconds <= 10) {
                $string = '10 секундийн өмнө';
            } else if ($distance_in_seconds >= 11 && $distance_in_seconds <= 20) {
                $string = '20 секундийн өмнө';
            } else if ($distance_in_seconds >= 21 && $distance_in_seconds <= 40) {
                $string = '30 секундийн өмнө';
            } else if ($distance_in_seconds >= 41 && $distance_in_seconds <= 59) {
                $string = '50 секундийн өмнө';
            } else {
                $string = '1 минутын өмнө';
            }
        }
    } else if ($distance_in_minutes >= 2 && $distance_in_minutes <= 44) {
        $string = '%minutes% минутын өмнө';
        $parameters['%minutes%'] = $distance_in_minutes;
    } else if ($distance_in_minutes >= 45 && $distance_in_minutes <= 89) {
        $string = '1 цагийн өмнө';
    } else if ($distance_in_minutes >= 90 && $distance_in_minutes <= 1439) {
        $string = '%hours% цагийн өмнө';
        $parameters['%hours%'] = round($distance_in_minutes / 60);
    } else if ($distance_in_minutes >= 1440 && $distance_in_minutes <= 2879) {
        $string = '1 өдрийн өмнө';
    } else if ($distance_in_minutes >= 2880 && $distance_in_minutes <= 43199) {
        $string = '%days% өдрийн өмнө';
        $parameters['%days%'] = round($distance_in_minutes / 1440);
    } else if ($distance_in_minutes >= 43200 && $distance_in_minutes <= 86399) {
        $string = '1 сарын өмнө';
    } else /* if ($distance_in_minutes >= 86400 && $distance_in_minutes <= 525959)
      {
      $string = '%months% сарын өмнө';
      $parameters['%months%'] = round($distance_in_minutes / 43200);
      }
      else if ($distance_in_minutes >= 525960 && $distance_in_minutes <= 1051919)
      {
      $string = '1 жилийн өмнө';
      }
      else
      {
      $string = '%years%  жилийн өмнө';
      $parameters['%years%'] = floor($distance_in_minutes / 525960);
      } */ {
        return date("Y-m-d H:i", $from_time);
    }
    return strtr($string, $parameters);
}

function display_options($parent, $level, $selected_cat_id) {
	// уг $parent ийн доод талын ангиллуудыг хэвлэдэг функц
    $product_cats = ProductCategoryTable::getListByParentId($parent);
    foreach ($product_cats as $product_cat) {

        //$disabled = $product_cat->hasChilds() ? 'disabled="disabled"' : '';
        $disabled = '';

        if ($product_cat->getId() == $selected_cat_id) {
            echo '<option value="' . $product_cat->getId() . '" selected="selected">';
                for ($i = 0; $i < $level; $i++)
                    echo "------";
                echo $product_cat->getName();
            echo '</option>';
        } else {
            echo '<option value="' . $product_cat->getId() . '" '.$disabled.'>';
                for ($i = 0; $i < $level; $i++)
                    echo "------";
                echo $product_cat->getName();
            echo '</option>';
        }
		// рекурсив-ээр дахин дуудаж байна.
        display_options($product_cat->getId(), $level + 1, $selected_cat_id);
    }
}

function display_category_rows($parent, $level) {
    // уг $parent ийн доод талын ангиллуудыг хэвлэдэг функц
    $product_cats = ProductCategoryTable::getListByParentIdForAdmin($parent);
    foreach ($product_cats as $product_cat) {
        echo '<tr><td>'.$product_cat->getCode().'</td><td>';
        for ($i = 0; $i < $level; $i++)
            echo "------------";

        echo $product_cat->getName();
        echo ' ('.$product_cat->getProductCount().')';
        echo '</td>';
        echo '<td>'.$product_cat->getAccessLogs().'</td>';
        echo '<td>'.$product_cat->getIcon().'</td>';
        echo '<td><a type="button" class="btn btn-default btn-xs mbs" href="'.url_for('@category_configure?id='.$product_cat->getId()).'"><i class="fa fa-edit"></i> Тохиргоо</a></td>';
        echo '<td>';
        if ($product_cat->getIsActive()):
            echo '<a href="'.url_for('@category_active?id='.$product_cat->getId()).'"
        type="button" class="btn btn-red btn-xs mbs"><i class="fa fa-edit"></i>&nbsp;Идэвхгүй болгох</a>';
        else:
            echo '<a href="'.url_for('@category_active?id='.$product_cat->getId()).'"
        type="button" class="btn btn-green btn-xs mbs"><i class="fa fa-edit"></i>&nbsp;Идэвхжүүлэх</a>';
        endif;
        echo '
        <a href="'.url_for('@category_edit?id='.$product_cat->getId()).'"
        type="button" class="btn btn-blue btn-xs mbs"><i class="fa fa-edit"></i>&nbsp;Засах</a>
        <a href="'.url_for('@category_delete?id='.$product_cat->getId()).'"
        type="button" onclick="return confirm(\'Устгахдаа итгэлтэй байна уу?\');" class="btn btn-danger btn-xs mbs"><i class="fa fa-trash-o"></i>&nbsp;Устгах</a></td></tr>';

        display_category_rows($product_cat->getId(), $level + 1);
    }
}

function display_content_category_options($parent, $level, $selected_cat_id) {
    // уг $parent ийн доод талын ангиллуудыг хэвлэдэг функц
    $product_cats = ContentCategoryTable::getListByParentId($parent);
    foreach ($product_cats as $product_cat) {

        //$disabled = $product_cat->hasChilds() ? 'disabled="disabled"' : '';
        $disabled = '';

        if ($product_cat->getId() == $selected_cat_id) {
            echo '<option value="' . $product_cat->getId() . '" selected="selected">';
            for ($i = 0; $i < $level; $i++)
                echo "------";
            echo $product_cat->getName();
            echo '</option>';
        } else {
            echo '<option value="' . $product_cat->getId() . '" '.$disabled.'>';
            for ($i = 0; $i < $level; $i++)
                echo "------";
            echo $product_cat->getName();
            echo '</option>';
        }
        // рекурсив-ээр дахин дуудаж байна.
        display_content_category_options($product_cat->getId(), $level + 1, $selected_cat_id);
    }
}

function display_content_category_rows($parent, $level) {
    // уг $parent ийн доод талын ангиллуудыг хэвлэдэг функц
    $product_cats = ContentCategoryTable::getListByParentId($parent);
    foreach ($product_cats as $product_cat) {
        $product_category = null;
        if ($product_cat->getProductCategoryId())
        {
            $product_category = ProductCategoryTable::getInstance()->find($product_cat->getProductCategoryId());
        }
        echo '<tr><td>'.$product_cat->getId().'</td><td>';
        for ($i = 0; $i < $level; $i++)
            echo "------------";
        echo $product_cat->getName();
        echo '</td>';
        echo '<td>'.$product_cat->getSlug().'</td>';
        echo '<td>'.($product_category ? $product_category->getName() : '').'</td>';
        echo '<td>'.($product_cat->getIsBlog() ? 'Тийм' : '').'</td>';
        echo '<td><a href="'.url_for('@content_category_edit?id='.$product_cat->getId()).'"
        type="button" class="btn btn-default btn-xs mbs"><i class="fa fa-edit"></i>&nbsp;Засах</a>
        <a href="'.url_for('@content_category_delete?id='.$product_cat->getId()).'"
        type="button" onclick="return confirm(\'Устгахдаа итгэлтэй байна уу?\');" class="btn btn-danger btn-xs mbs"><i class="fa fa-trash-o"></i>&nbsp;Устгах</a></td></tr>';

        display_content_category_rows($product_cat->getId(), $level + 1);
    }
}

function options_for_select_organizations($parent, $level, $selected_id) {
// уг $parent ийн доод талын division үүдийг хэвлэдэг функц

    $organizations = Doctrine_Core::getTable('Organization')->getByParentId($parent, $level);

    foreach ($organizations as $organization) :

        if ($selected_id == $organization->getId()):
            ?> 

            <option value="<?php echo $organization->getId() ?>" selected="selected">

            <?php
            for ($i = 0; $i < $level; $i++)
                echo "------";

            echo $organization->getName();
            ?>

            </option>

            <?php
        else:
            ?>

            <option value="<?php echo $organization->getId() ?>">

            <?php
            for ($i = 0; $i < $level; $i++)
                echo "------";

            echo $organization->getName();
            ?>

            </option>

        <?php
        endif;

        options_for_select_organizations($organization->getId(), $level + 1, $selected_id);

    endforeach;
}

function print_for_table_organizations($parent, $level, $counter) {

// уг $parent ийн доод талын division үүдийг хэвлэдэг функц

    $organizations = Doctrine_Core::getTable('Organization')->getByParentId($parent, $level + 1);

    $cntr = 0;

    foreach ($organizations as $organization) {

        $cntr++;

        echo '<tr><td align="center">' . $counter . '.' . $cntr . '</td><td>';

        for ($i = 0; $i <= $level; $i++)
            echo "------";

        echo $organization->getName();
        ?>

        </td>

        <td width="50" align="center"><?php echo link_to(image_tag('/images/icons/edit.png', array('border' => 0)), '@organization_edit?id=' . $organization->getId()) ?></td>

        <td width="50" align="center"><?php echo link_to(image_tag('/images/icons/delete.png', array('border' => 0)), '@organization_delete?id=' . $organization->getId(), array('confirm' => 'Та устгахдаа итгэлтэй байна уу?')) ?></td>

        </tr>

        <?php
        print_for_table_organizations($organization->getId(), $level + 1, $cntr);
    }
}

function convert($haystack) {
    $words = array();

    $symbol = '#';
    $space = ' ';
    $length = strlen($haystack);
    for ($i = 0; $i <= $length; $i++) {
        $char = substr($haystack, $i, 1);
        $word = '';
        if ($char == $symbol) {
            $start = $i;
            $needle = substr($haystack, $start - strlen($haystack));

            $j = 0;
            do {
                $j++;
                $total = $start + $j;
            } while (substr($needle, $j, 1) != $space && $total <= $length && substr($needle, $j, 1) != $symbol);

            $end = $j;
            $word = substr($haystack, $start, $end);
            $words[] = $word;
        }
    }

    if (count($words)) {
        foreach ($words as $w) {
            $replace_word = '<a href="' . url_for('@search?q=' . substr($w, 1 - strlen($w))) . '">' . $w . '</a>';
            $haystack = str_replace($w, $replace_word, $haystack);
        }
    }
    return $haystack;
}

function trim_text($input, $length, $ellipses = true, $strip_html = true) {
    //strip tags, if desired
    if ($strip_html) {
        $input = strip_tags($input);
    }
    //no need to trim, already shorter than trim length
    if (strlen($input) <= $length) {
        return $input;
    }
    //find last space within length
    $last_space = strrpos(substr($input, 0, $length), ' ');
    $trimmed_text = substr($input, 0, $last_space);
    //add ellipses (...)
    if ($ellipses) {
        $trimmed_text .= '...';
    }
    return $trimmed_text;
}

function renderError($form) {
    if ($form->hasErrors()) {
        echo '<div class="error">';
        foreach ($form as $field) {
            echo $field->renderError();
        }
        echo $form->renderGlobalErrors();
        echo '</div>';
    }
}

function renderSuccess($sf_user) {
    if ($sf_user->hasFlash('success')) {
        echo '<div class="success">';
        echo $sf_user->getFlash('success');
        echo '</div>';
    }
}
?>
