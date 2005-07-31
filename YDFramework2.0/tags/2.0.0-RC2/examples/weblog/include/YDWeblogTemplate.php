<?php

    // Template BBCode modifier
    function YDTplModBBCode( $text ) {
        $cls = new YDBBCode();
        return $cls->toHtml( $text, true, false, false, YDRequest::getCurrentUrl() );
    }

    // Template t modifier
    function YDTplModT( $params ) {
        if ( @ $params['lower'] == 'yes' ) {
            return isset( $params['w'] ) ? strtolower( t( $params['w'] ) ) : '';
        } else {
            return isset( $params['w'] ) ? t( $params['w'] ) : '';
        }
    }

    // Template Date modifier
    function YDTplModDate( $text, $format=null ) {
        if ( is_null( $format ) ) {
            $format = '%A, %d %b %Y';
        }
        return ucwords( strtolower( YDTemplate_modifier_date_format( $text, $format ) ) );
    }

    // Create a link using the ID and given base
    function YDTplModLinkWithID( $base, $item, $suffix=null ) {
        $base .= ( strpos( $base, '?' ) === false ) ? '?id=' : '&id=';
        if ( is_numeric( $item ) ) {
            $base .= $item;
        } else {
            $base .= isset( $item['id'] ) ? $item['id'] : $item;
        }
        if ( ! is_null( $suffix ) ) {
            $base = $base . $suffix;
        }
        return YDUrl::makeLinkAbsolute( $base, YDRequest::getCurrentUrl( true ) );
    }

    // Provide a link to a weblog item
    function YDTplModLinkItem( $id, $suffix='' ) {
        return YDTplModLinkWithID( 'item.php', $id, $suffix );
    }

    // Provide a link to a weblog image
    function YDTplModLinkItemImages( $id ) {
        return YDTplModLinkWithID( 'item.php', $id, '#images' );
    }

    // Provide a link to a weblog comment
    function YDTplModLinkItemComment( $id ) {
        return YDTplModLinkWithID( 'item.php', $id, '#comment' );
    }

    // Provide a link to a weblog item comment form
    function YDTplModLinkItemRespond( $id ) {
        return YDTplModLinkWithID( 'item.php', $id, '#respond' );
    }

    // Provide a link to a category
    function YDTplModLinkCategory( $id ) {
        return YDTplModLinkWithID( 'category.php', $id );
    }

    // Provide a link to a page
    function YDTplModLinkPage( $id ) {
        return YDTplModLinkWithID( 'page.php', $id );
    }

    // Provide a link to a link
    function YDTplModLinkLink( $id ) {
        return YDTplModLinkWithID( 'link.php', $id );
    }

    // Provide a link to a thumbnail
    function YDTplModLinkThumb( $img ) {
        return YDUrl::makeLinkAbsolute(
            YDConfig::get( 'dir_uploads', 'uploads' ) . '/' . dirname( $img->relative_path ) . '/m_' . basename( $img->relative_path )
        );
    }

    // Provide a link to a small thumbnail
    function YDTplModLinkThumbSmall( $img ) {
        return YDUrl::makeLinkAbsolute(
            YDConfig::get( 'dir_uploads', 'uploads' ) . '/' . dirname( $img->relative_path ) . '/s_' . basename( $img->relative_path )
        );
    }

    // Get a text containing the number of comments
    function YDTplModTextNumComments( $item, $showIfNone=false ) {
        if ( $item['num_comments'] == '0' ) {
            return $showIfNone ? strtolower( t( 'no_comments_yet' ) ) : '';
        } elseif ( $item['num_comments'] == '1' ) {
            return $item['num_comments'] . ' ' . strtolower( t( 'comment' ) );
        } else {
            return $item['num_comments'] . ' ' . strtolower( t( 'comments' ) );
        }
    }

    // Get a text containing the number of images
    function YDTplModTextNumImages( $item, $showIfNone=false ) {
        if ( $item['num_images'] == '0' ) {
            return $showIfNone ? strtolower( t( 'no_images_yet' ) ) : '';
        } elseif ( $item['num_images'] == '1' ) {
            return $item['num_images'] . ' ' . strtolower( t( 'image' ) );
        } else {
            return $item['num_images'] . ' ' . strtolower( t( 'images' ) );
        }
    }

?>
