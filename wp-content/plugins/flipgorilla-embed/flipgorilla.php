<?php
/**
 * Flipgorilla-Plugin
 *
 * @package   flipgorilla
 * @author    Florian Neumann <fne@reichl.cc> <di.neumann.florian@gmail.com>, Gerald Aistleitner <gai@reichl.cc>
 * @license   GPL-2.0+
 * @link      http://www.reichlundpartner.com/Die-Agentur
 * @copyright 2013 Reichl & Partner
 *
 * @wordpress-plugin
 * Plugin Name: flipgorilla
 * Plugin URI:  http://www.flipgorilla.com
 * Description: Plugin for including and managing Flipgorilla-Flipbooks in Wordpress
 * Version:     1.0.3
 * Author:      Reichl und Partner emarketing GmbH
 * Author URI:  http://www.forgetech.org
 * Text Domain: eng
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: /lang
 */
class Flipgorilla {

  /**
   * Constants
   */
  const name = 'flipgorilla';
  const slug = 'flipgorilla';
  const manageInfo = "Use the Manage-Screen to edit Flipbook default-options as well as to edit Flipbook properties ";

  public static $idHelpText =  "Enter a valid Flipgorilla Flipbook id here! A valid Flipbook is a numeric value like '1',
  it is also possible to paste a full flipgorilla flipbook-link in format like 'http://www.flipgorilla.com/p/ 22737216270762022/show'.
  This link will be automatically transformed to the numeric id-value.";
  public static $widthHelpText = "The width the embedded flipbook should have, as a numeric value without an attached units like 'px, %, ...'! Note that this setting is overwritten
  when a flipbooks responsive-property is set to 'true', then using the full width of the container it is embedded.";
  public static $heightHelpText = "The height the embedded flipbook should have, as a numeric value without any attached units like 'px, %, ...'! Note that this setting is overwritten
  when a flipbooks layout-property is set to 'Double Page'-Layout, in this case the height of the embeded flipbook will be about 60% of the defined height-value.";
  public static $layoutHelpText = "This option defines if an embedded flipbook should present one or two pages. Note that if set to 'Double-Page' and there is not enough space to present
  both pages, the flipbook falls back to 'Single Page'-Layout";
  public static $linkHelpText = "This text will be used for the content of the link shown, when embeding of the flipbook is not possible! Mostly this is the case when a javascript-error
  appears on the page the flipbook is embedded in. Must not be empty!";
  public static $responsiveHelpText = "When this option is set to true, the defined width value will be overwritten with a value of '100%' forcing the flipbook to use the whole
  width of the container it is embeded too.";
  public static $main_page_url;
  public static $manage_page_url;
  public static $help_page_url;

  /**
   * Constructor
   */
  function __construct() {
    // Register an activation hook for the plugin
    register_activation_hook( __FILE__, array( &$this, 'activate_flipgorilla' ) );
    // Register an activation hook for the plugin
    register_deactivation_hook( __FILE__, array( &$this, 'deactivate_flipgorilla' ) );
    // Hook up to the init action
    add_action( 'init', array( &$this, 'init_flipgorilla' ) );
  }

  /**
   * Runs when the plugin is activated
   */
  function activate_flipgorilla() {
    self::store_defaults();
  }

  /**
   * Runs when the plugin is activated
   */
  function deactivate_flipgorilla() {
    self::store_defaults( false );
    self::store_flipbooks( false );
  }

  /**
   * Runs when the plugin is initialized
   */
  function init_flipgorilla() {

    // Setup localization
    load_plugin_textdomain( self::slug, false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );
    // Load JavaScript and stylesheets
    $this->register_scripts_and_styles();

    if ( is_admin() ) {
      // this will run when in the WordPress admin
    } else {
      // this will run when on the frontend
    }

    self::$main_page_url = admin_url( '/admin.php?page=flipgorilla-main' );
    self::$manage_page_url = admin_url( '/admin.php?page=flipgorilla-manage' );
//    self::$help_page_url = admin_url( '/admin.php?page=flipgorilla-help' );


    // Register the shortcode for including a flipbook [flipgorilla]
    add_shortcode( 'flipgorilla', array( &$this, 'get_flipgorilla_embed_code' ));
    // Add to admin_menu function
    add_action('admin_menu', array( &$this,'flipgorilla_options_panel'));
    // Add action for calling ajax-request from client.
    add_action( 'admin_footer', array( &$this, 'flipgorilla_ajax_javascript' ));
    // Add action for allowing ajax-data handling
    add_action('wp_ajax_flipgorilla_ajax', array( &$this, 'flipgorilla_ajax_callback'));
  }

  /**
   * Callback for receiving ajax-requests from frontend
   */
  function flipgorilla_ajax_callback() {
    // This is how you get access to the database
    global $wpdb;

    $append = $_POST['append'];

    // WORKAROUND - JSON: Couldn't get a valid json-string, that could be processed proberly, from
    // Javascript. You i had to use this ugly workaround to geht json_decode working.
    $defaults = json_decode( '{'.stripslashes( str_replace("'", '"', $_POST['defaults'])).'}') ;
    if( $defaults != false ){
      $response = self::store_defaults( $defaults );
    }

    // WORKAROUND - JSON: As it was not possible for me to get a clean json-formatted
    // string over wordpress-wire flipbooks are stored in a custom formatted string
    $flipbooks = stripslashes( str_replace("'", '"', $_POST['flipbooks']));
    if( $flipbooks != 'false'){
      // Append new Flipgorilla-Data
      if( $append == 'true' ){
        $response = self::store_flipbooks( $flipbooks, $append );
      }
      // Replace/Update all Flipbook-Data
      else {
        $response = self::store_flipbooks( $flipbooks );
      }
    }

    // echo json_encode( $response );

    die(); // this is required to return a proper result
  }

  /**
  * Methode is used to store received post-data in json-format
  * to wordpress options-table
  */
  function flipgorilla_ajax_javascript() {
  ?>
   <script type="text/javascript" >
    jQuery( document ).ready( function( $ ) {

      // Bind click-events to submit-Buttons
      $( '.flipgorilla-manage input[type="submit"], .flipgorilla-main input[type="submit"]' ).click( function( event ){
        event.preventDefault();
        var data = createData( this ),
        form = $( this ).closest( 'form' );

        $('input[type="text"]', $(form) ).each(function( index ){
          validate( this );
        });

        errorCount = $('span.error', $(form) ).length;

        if(errorCount == 0 ){
//          console.log( 'submitData:' );
//          console.log( data );
//          console.log( 'error:' );
//          console.log( errorCount );
          submitData( data );
        }
      });

      // Bind validation on input change
      var hiddenSubmitSection = $('.hidden-submit');
      $( hiddenSubmitSection ).hide();

      // Bind validation on input change
      $('.flipgorilla-manage input[type="text"], .flipgorilla-main input[type="text"]').change(function(event){
        event.preventDefault();
        validate( this );
      })


      // Bind click-events to delete-Buttons
      $( '.flipgorilla-manage a.delete, .flipgorilla-main a.delete' ).click( function( event ){
          event.preventDefault();
          deleteFlipbook( this );
      });

      // Bind click-events to cancel-Buttons
      // $( '.flipgorilla-manage a.cancel, .flipgorilla-main a.cancel' ).click( function( event ){
      //     event.preventDefault();
      //     cancelFlipbookDeletation( this );
      // });

      // Bind click-events to cancel-Buttons
      $( '.flipgorilla-manage form a.help-icon, .flipgorilla-main form a.help-icon' ).hover( function( event ){
          event.preventDefault();
          showHelpText( this );
      },
      function(event){
        event.preventDefault();
          hideHelpText( this );
      });

      function showHelpText( helpBtn ){
//        console.log('showHelpText');
        var helpText = $( helpBtn ).attr('data-help');
        $( helpBtn ).append( '<div class="help-box">' + helpText + '</div>' );
      }

      function hideHelpText( helpBtn ){
//        console.log('hideHelpText');
        $( helpBtn ).empty();
      }

      function validate( element ){
//        console.log( "validation:" );
        var isValid = false;
//        console.log( element );
        if( $( element ).attr('name') == 'link'  ){
//          console.log('Empty validation: ');
          isValid = isEmpty( $( element ).val(), element );
        }
        else{
//          console.log('Numeric validation: ');
          isValid = isNumeric( $( element ).val(), element );
        }
        return isValid;
      }

      function isNumeric( value, element ){
        var isNumeric = false;
        isNumeric = checkIfNumeric( value );

        // Check if non-numeric value on id-field is an flipgorilla-url
        if( $( element ).attr( "name" ) == "id"  && !isNumeric ){
          var splitted = value.split( '/' );
          // Check for flipgorilla.com url to validate this is not just and url
          if( splitted[2] == "www.flipgorilla.com" ){
            isNumeric = checkIfNumeric( splitted[4] )
            if( isNumeric ) {
              $( element ).val( splitted[4] );
            }
          }
        }

        if( !isNumeric ){
          if( !$( element ).parent().hasClass( 'error' ) ){
            $( element ).wrap( '<span class="error">' );
          }
        } else if( $( element ).parent().hasClass('error') ) {
          $( element ).unwrap();
        }
//        console.log( isNumeric );
        return isNumeric;
      }

      function checkIfNumeric( value ){
        return /^-?(?:\d+|\d{1,3}(?:,\d{3})+)(?:\.\d+)?$/.test( value );
      }

      function isEmpty(value, element ){
        var isEmpty = $.trim( value ) != "" ? true : false;

        if( !isEmpty ) {
          if( !$( element ).parent().hasClass( 'error' ) ){
            $( element ).wrap( '<span class="error">' );
          }
        }
        else if( $( element ).parent().hasClass('error') ) {
          $( element ).unwrap();
        }

        return isEmpty;
      }

      // Delete Button must be contained in Form to be submited
      function deleteFlipbook( deleteBtn ){
        var $flipbookToDelete = $( deleteBtn ).closest( '.flipbook' ),
        form = $( deleteBtn ).closest( 'form' );
        // Hide items before deletation to allow chancel functionality
        // Items get removed when data is collected for submission
        // $flipbookToDelete.hide();
        // $flipbookToDelete.attr('class','flipbook-to-delete');

        // Just remove the flipbook
        $flipbookToDelete.remove();
        updateFlipbookIndexes( form );
        if( $( 'flipbook-attr-list' , $( form ) ).length == 0  ){
          $( hiddenSubmitSection ).show();
        }
      }

      // function cancelFlipbookDeletation( cancelBtn ){
      //   form = $( cancelBtn ).closest( 'form' );
      //   flipbooksToDelete = $('.flipbook-to-delete', $( form ) )
      //   $(flipbooksToDelete).attr('class', 'flipbook');
      //   updateFlipbookIndexes( form );
      //   $(flipbooksToDelete).show();
      // }

      // Updates the indexes of flipbooks
      function updateFlipbookIndexes( form ){
        $( '.flipbook h3.headline', $( form ) ).each( function( index ){
          $( this ).text( 'Edit Flipbook ' + (index+1) )
        });

        $( '.flipbook .shortcode', $( form ) ).each( function( index ){
          // Exchange number in splitted shortcode
          var splitted= $( this ).text().split("'"),
          merged = splitted[0] + "'" + (index+1) + "'" + splitted[2];
          $( this ).text( merged );
        });
      }

      // Sends the data to wordpress-backend
      function submitData( data ){
        // Since wordpress 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
        $.post(ajaxurl, data, function(response) {
          if(response)
          {
            alert('Error storing values! Please try again!');
          } else {
            alert('Values stored successfully!');
          }
        });
      }

      // Submit Button must be contained in Form to be submited
      function createData( submitBtn ){

          var formToSubmit = $( submitBtn ).closest( 'form' )
          , collectedData
          , data = {
            action: "flipgorilla_ajax"
            , flipbooks: false
            , defaults: false
          }

          // Remove all flipbooks marked for deletation
          // $('.flipbook-to-delete').remove();
          // Collect data
          collectedData = collectData( formToSubmit )
          // Set data and additional attributes
          switch( $( formToSubmit ).attr('id') ){
            case "add-flipbook-form":
              data.append = true;
              data.flipbooks = collectedData;
              break;

            case "set-defaults-form":
              data.append = false;
              data.defaults = collectedData;
              break;

            case "update-flipbooks-form":
//              console.log( 'update-flipbooks-form' );
              data.append = false;
              data.flipbooks = collectedData;
              break;

            default:
              throw new Error( "Form Id not found!" )
              break;
          }

          return data;
      }

    function collectData( form ){

      var data = ""
      , lists = $( 'ul.flipbook-attr-list', $( form ) )
      , id
      , witdh
      , height
      , layout
      , link
      , responsive;

      // WORKAROUND - JSON: Couldn't use json.stringify because it results in a string like: "{"key":"value"}"
      // which couldn't be parsed proberly in php-code. So i create my own invalid json-string for sending data
      // to the php-code (Format: "'key':'value'")
      $( lists ).each( function( index ){
        data += "'id': " + "'" + $( 'input[ name="id" ]', $( this ) ).val() + "', ";
        data += "'width': " + "'" + $( 'input[ name="width" ]', $( this ) ).val() + "', ";
        data += "'height': " + "'" + $( 'input[ name="height" ]', $( this ) ).val() + "', ";
        data += "'layout': " + "'" + $( 'select[ name="layout" ]', $( this ) ).val() + "', ";
        data += "'link': " + "'" + $( 'input[ name="link" ]', $( this ) ).val() + "', ";
        data += "'responsive': " + "'" + $( 'select[ name="responsive" ]', $( this ) ).val() + "'";
        if(lists.length > 1 && index+1 < lists.length){
          data += '||';
        }
      });
//      console.log( data );
      return data;
    }

});
</script>
  <?php
  }

  /**
  * Methode is used to store received post-data in json-format
  * to wordpress options-table
  */
  function store_flipbooks( $flipbooks, $append = false ) {

    // If only one flipbook is received from ajax script, this means its
    if( $append ) {
      $storedFlipbooks = get_option( 'flipbooks' );

      if($storedFlipbooks != "")
      {
        $flipbooks = $storedFlipbooks.'||'.$flipbooks;
      }
    }

    return update_option( 'flipbooks',  $flipbooks );
  }

  /**
  * Methode is used to store received post-data in json-format
  * to wordpress options-table
  */
  function store_defaults( $defaults = false ) {

    if( !$defaults ){
      $defaults->id = 'Please enter your flipbook id';
      $defaults->width = 600;
      $defaults->height = 600;
      $defaults->layout = 1;
      $defaults->link = 'Got to Flipbook';
      $defaults->responsive = false;
    }

    // Ugly fix for default-placeholder, turns undefined after editing of
    // defaults
    if( $defaults->id == 'undefined' ){
      $defaults->id = 'Please enter your flipbook id';
    }

    return update_option( 'defaults', $defaults );
  }

  /**
   * Runs when the plugin is activated
   */
  function flipgorilla_options_panel() {
    add_menu_page('Flipgorilla Options', 'Flipgorilla', 'manage_options', 'flipgorilla-main', array( &$this, 'flipgorilla_main'), plugins_url( 'assets/logo16x16.png' , __FILE__ ) );
    add_submenu_page( 'flipgorilla-main', 'Flipgorilla - Manage Flipbooks', 'Manage', 'manage_options', 'flipgorilla-manage', array( &$this, 'flipgorilla_manage') );
//    add_submenu_page( 'flipgorilla-main', 'Flipgorilla - Help Page', 'Help Page', 'manage_options', 'flipgorilla-help', array( &$this, 'flipgorilla_help') );
  }

  /**
   * Template for main page of the flipgorilla-plugin
   */
  function flipgorilla_main() {

    $default = get_option( 'defaults' );

    echo ' <div class="flipgorilla-main flipgorilla">
    <div class="wrap">
      <span class="flipgorilla-icon">
      </span>
      <h2>Flipgorilla</h2>
<!--      <a class="help-icon" href="'.self::$help_page_url.'"></a> -->
    </div>

    <div class="info">
      <h3> Take a look at our website and create a free account! </h3>
      If you are already a flipgorilla user, login to view your available flipbooks. Have a great flipping day!
    </div>

    <iframe id="embed-flipgorilla-website" src="http://www.flipgorilla.com/"></iframe>

    <div class="info">
      <form id="add-flipbook-form" method="post">
        <h3>Add new flipgorilla:</h3>
    ';

    self::edit_flipgorilla_attr_template( $default );

    echo '
        <input type="submit" value="Submit">
      </form>';
    echo '</div><div class="required"><span>All Fields marked with</span><span class="star"> *</span><span> are required!</span></div>';
    echo '
    <div class="option-pages">
      <a class="info-link" href="'.self::$manage_page_url.'" >
        <div class="info">
            <h3> >> Go to Manage-Screen</h3>
            <h4>'.self::manageInfo.'</h4>
        </div>
      </a>
    </div>
    </div>';
  }

   /**
   * Template for the manage page of the flipgorilla-plugin
   */
  function flipgorilla_manage(){

    $flipbooks = get_option( 'flipbooks' );
    $defaults = get_option( 'defaults' );

    echo '<div class="flipgorilla-manage flipgorilla">';
    echo '<div class="wrap"><span class="flipgorilla-icon"></span><h2>Flipgorilla - Manage Flipbooks</h2><!--<a class="help-icon" href="'.self::$help_page_url.'"></a>--></div>';
    echo '<div class="info">';
    echo '<form id="set-defaults-form" method="post"><h3>Edit Flipbook default-options:</h3>';
      self::edit_flipgorilla_attr_template( $defaults, false );
    echo '<input type="submit" value="Submit"></form>';
    echo '</div>';

    // echo 'unexploded';
    // var_dump( $flipbooks );

    $flipbooks = explode( "||", $flipbooks );
    $count = 1;

    // echo 'exploded';
    // var_dump( $flipbooks );

    if( $flipbooks[0] != "" ){
      echo '<div class="info">';
      foreach ( $flipbooks as $flipbook ) {
        $flipbookObj = json_decode( '{'.$flipbook.'}' );
        echo '<form id="update-flipbooks-form" method="post">';
        echo '<div class="flipbook"><h3 class="inline headline">Edit Flipbook '.( $count ).'</h3><h4 class="inline"> ( </h4> <a class="delete">Delete</a> <h4 class="inline"> ) </h4> <h3 class="inline">:</h3>';
          self::edit_flipgorilla_attr_template( $flipbookObj );
        $shortcode = self::get_flipgorilla_shortcode( $count );
        echo '<h4>Copy this into wordpress-editor to embed flipgorilla-flipbook: <div class="shortcode">'.$shortcode.'</div><input class="update" type="submit" value="Update"></h4>';
        echo '</div>';
        $count++;
      }

      echo '<div class="hidden-submit">';
      echo '<h3>All Flipbooks deleted! Submit Form to store changes!</h3>';
      echo '<input class="update" type="submit" value="Update">';
      echo '</div>';
      echo '</form>';
      echo '</div>';
      echo '<div class="required"><span>All Fields marked with</span><span class="star"> *</span><span> are required!</span></div>';
      echo '</div>';
    }
    else
    {
      echo '<a href="'.self::$main_page_url.'" ><div class="info"><h3>No flipgorilla flipbooks defined yet! Go to main-page to define flipbooks.</h3></div></a>';
    }
  }

  /**
   * Template for the manage page of the flipgorilla-plugin
   */
  function flipgorilla_help(){

    $flipbooks = get_option( 'flipbooks' );
    $defaults = get_option( 'defaults' );

    echo '<div class="flipgorilla-help">';
    echo '<div class="wrap"><span class="flipgorilla-icon"></span><h2>Flipgorilla - Help Page</h2></div>';
    echo '</div>';

  }

  /**
   * Methode creates the template used to edit flipbook properties
   */
  function edit_flipgorilla_attr_template( $attr, $showId = true ){

    if( $attr->responsive == 'true' )
    {
      $responsiveOptions = '<option selected value="true"> True </option><option value="false"> False </option>';
    }
    else
    {
      $responsiveOptions = '<option selected value="false"> False </option><option value="true"> True </option>';
    }

    if( $attr->layout == '1' )
    {
      $layoutOptions = '<option selected value="1"> Single Page </option>
                              <option value="2"> Double Page </option>';
    }
    else
    {
       $layoutOptions = '<option value="1"> Single Page </option>
                      <option selected value="2"> Double Page </option>';
    }

    echo '<ul class="flipbook-attr-list">';

    if( $showId )
    {
      if( is_numeric( $attr->id ) ){
        echo '<li><label for="id-'.$attr->id.'" >Id:<span class="star"> *</span></label><input type="text" value="'.$attr->id.'" id="id-'.$attr->id.'" name="id"><a class="help-icon" data-help="'.self::$idHelpText.'" href="'.self::$help_page_url.'"></a></li>';
      }
      else {
         echo '<li><label for="id-'.$attr->id.'" >Id:<span class="star"> *</span></label><input type="text" placeholder="'.$attr->id.'" id="" name="id"><a class="help-icon" data-help="'.self::$idHelpText.'" href="'.self::$help_page_url.'"></a></li>';
      }
    }
    echo $widthHelpText;
    echo '<li><label for="width">Width:<span class="star"> *</span></label><input type="text" value="'.$attr->width.'" name="width"><a class="help-icon" data-help="'.self::$widthHelpText.'" href="'.self::$help_page_url.'"></a></li>
      <li><label for="height">Height:<span class="star"> *</span></label><input type="text" value="'.$attr->height.'" name="height"><a class="help-icon" data-help="'.self::$heightHelpText.'" href="'.self::$help_page_url.'"></a></li>
      <li><label for="layout">Layout:<span class="star"> *</span></label>
        <select name="layout">'
          .$layoutOptions.
        '</select><a class="help-icon" data-help="'.self::$layoutHelpText.'" href="'.self::$help_page_url.'"></a>
      </li>
      <li><label for="link">Link:<span class="star"> *</span></label><input type="text" value="'.$attr->link.'" name="link"><a class="help-icon" data-help="'.self::$linkHelpText.'" href="'.self::$help_page_url.'"></a></li>
      <li><label for="responsive" >Responsive:<span class="star"> *</span></label>
        <select name="responsive">'
          .$responsiveOptions.
        '</select><a class="help-icon" data-help="'.self::$responsiveHelpText.'" href="'.self::$help_page_url.'"></a>
      </li>
      </ul>';
  }

  /**
   * Returns String to use in wordpress editor
   */
  function get_flipgorilla_shortcode( $flipbook_id ){
    return "[flipgorilla flipbook='".$flipbook_id."']";
  }

  /**
   * Methode is used to store received post-data in json-format
   * to wordpress options-table
   */
  function get_flipgorilla_embed_code( $attr ) {
      $defaults = get_option( 'defaults' );
      $flipbooks = get_option( 'flipbooks' );
      $embedCode = false;

      // Preset varibales with defaults
      $id = $defaults->id;
      $height = $defaults->height;
      $width = $defaults->width;
      $layout = $defaults->layout;
      $link = $defaults->link;
      $responsive = $defaults->responsive;

      // Check if flipbook-attr is set and such flipbook exists in options table
      if( $attr[ 'flipbook' ]  )
      {
        $flipbooksExp = explode( "||", $flipbooks );
        $flipbookObj = json_decode("{".$flipbooksExp[ ( $attr[ 'flipbook' ]-1 ) ]."}") ;
      }

      // If flipbook is found for shorthand like [flipgorilla flipbook='1']
      // set it's properties.
      if( $flipbookObj->id ){
        $id = $flipbookObj->id;
        $height = $flipbookObj->height;
        $width = $flipbookObj->width;
        $layout = $flipbookObj->layout;
        $link = $flipbookObj->link;
        $responsive = $flipbookObj->responsive;
      }

      // If additional shorthand-properties are set like
      // [flipgorilla id='1' width='600' height='600'
      // layout='1' link='test' responsive="false"]
      // override default or previously set values
      if( $attr[ 'id' ] ){
         $id = $attr[ 'id' ];
      }
      if( $attr[ 'width' ] ){
         $width = $attr[ 'width' ];
      }
      if( $attr[ 'height' ] ){
         $height = $attr[ 'height' ];
      }
      if( $attr[ 'layout' ] ){
         $layout = $attr[ 'layout' ];
      }
      if( $attr[ 'link' ] ){
         $link = $attr[ 'link' ];
      }
      if( $attr[ 'responsive' ] ){
         $responsive = $attr[ 'responsive' ];
      }

      // The Plugin uses the std. values for witdh and height to achieve single/double-page-layouts,
      // as seen in the embed-code created on flipgorilla.com.
      // '1' is std. layout and its values are defined by default
      // '2' is double-page-layout
      if( $layout == 2){
        $height = ( $height*0.61 ).'px';
      } else {
        $height = $height.'px';
      }
      // 'Responsive' is basically setting the with property to 100%
      if( $responsive == 'true'){
        $width = '100%';
      } else {
        $width = $width.'px';
      }
      // Set id to "No flipbook-id set!" if id still contains default value at this point
      if( is_numeric( $id )){
        $embedCode = '<div name="flipgorilla" data-id="'.$id.'" style="width: '.$width.'; height: '.$height.';"><a href="http://www.flipgorilla.com/p/'.$id.'/show" target="_blank">'.$link.'</a></div><script type="text/javascript" src="http://www.flipgorilla.com/fg.js"></script>';
      }
      else
      {
        $embedCode = "No flipbook found!";
      }

      return $embedCode;
  }

  /**
   * Registers and enqueues stylesheets for the administration panel and the
   * public facing site.
   */
  private function register_scripts_and_styles() {
    if ( is_admin() ) {
      $this->load_file( self::slug . '-admin-style', '/css/admin.css' );
      // $this->load_file( self::slug . '-admin-script', '/js/jquery-1.8.2.min.js', true );
      // $this->load_file( self::slug . '-admin-script', '/js/jquery-validation-1.9.0/lib/jquery.validate.min.js', true );
    }
  } // end register_scripts_and_styles

  /**
   * Helper function for registering and enqueueing scripts and styles.
   *
   * @name  The   ID to register with WordPress
   * @file_path   The path to the actual file
   * @is_script   Optional argument for if the incoming file_path is a JavaScript source file.
   */
  private function load_file( $name, $file_path, $is_script = false ) {

    $url = plugins_url($file_path, __FILE__);
    $file = plugin_dir_path(__FILE__) . $file_path;

    if( file_exists( $file ) ) {
      if( $is_script ) {
        wp_register_script( $name, $url, array('jquery') ); //depends on jquery
        wp_enqueue_script( $name );
      } else {
        wp_register_style( $name, $url );
        wp_enqueue_style( $name );
      } // end if
    } // end if

  } // end load_file

} // end class

new Flipgorilla();

?>