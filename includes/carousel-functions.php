<?php

/**
 * Verifies that the core WPBookList plugin is installed and activated - otherwise, the Extension doesn't load and a message is displayed to the user.
 */
function wpbooklist_carousel_core_plugin_required() {

  // Require core WPBookList Plugin.
  if ( ! is_plugin_active( 'wpbooklist/wpbooklist.php' ) && current_user_can( 'activate_plugins' ) ) {

    // Stop activation redirect and show error.
    wp_die( 'Whoops! This WPBookList Extension requires the Core WPBookList Plugin to be installed and activated! <br><a target="_blank" href="https://wordpress.org/plugins/wpbooklist/">Download WPBookList Here!</a><br><br><a href="' . admin_url( 'plugins.php' ) . '">&laquo; Return to Plugins</a>');
  }
}


// Adding the front-end ui css file for this extensionn
function wpbooklist_jre_carousel_frontend_ui_style() {
    wp_register_style( 'wpbooklist-carousel-frontend-ui', CAROUSEL_ROOT_CSS_URL.'carousel-frontend-ui.css' );
    wp_enqueue_style('wpbooklist-carousel-frontend-ui');
}


// Function to allow users to specify which table they want displayed by passing as an argument in the shortcodeee
function wpbooklist_carousel_shortcode_function($atts){
  global $wpdb;
  extract(shortcode_atts(array(
          'table' => $wpdb->prefix."wpbooklist_jre_saved_book_log",
          'coverwidth' => '103',
          'coverheight' => '170',
          'speed' => '5000',
          'titlecount' => '3',
          'transition' => 'rotateleft',
          'action' => 'colorbox'
  ), $atts));

  if(isset($atts['table'])){
    $table =  $atts['table'];
  }

  if(isset($atts['speed'])){
    $speed = ($atts['speed']*1000);
  }

  if(isset($atts['titlecount'])){
    $titlecount = $atts['titlecount'];
  }

  if(isset($atts['transition'])){
    $transition = $atts['transition'];
  }

  if(isset($atts['coverwidth'])){
    $coverwidth = $atts['coverwidth'];
  }

  if(isset($atts['coverheight'])){
    $coverheight = $atts['coverheight'];
  }

  if(isset($atts['action'])){
    $coverheight = $atts['action'];
  }


  $atts_array = array(
    'table' => $table,
    'speed' => $speed,
    'titlecount' => $titlecount,
    'transition' => $transition,
    'coverheight' => $coverheight,
    'coverwidth' => $coverwidth,
    'action' => $action
  );


  ob_start();
  include_once( CAROUSEL_ROOT_INCLUDES_UI . 'class-frontend-carousel-ui.php');
  $carousel = new WPBookList_Carousel_UI($atts_array);
  echo $carousel->html_output;
  return ob_get_clean();
}

// For setting up initial carousel UI
function carousel_initial_ui_action_javascript() { 
  ?>
    <script type="text/javascript" >
    "use strict";
    jQuery(document).ready(function($) {

      if($('#wpbooklist_carousel_main_display_div').css('width') != undefined){
        $('.wpbooklist-carousel-shortcode-atts-div-class').each(function(index){

          // Give a unique ID
          $(this).attr('id', 'wpbooklist-carousel-shortcode-atts-div'+'-'+index);
          var uniqueId = $(this).attr('id');

          var argsDiv = $(this);
          var table = argsDiv.attr('data-table');
          var containerwidth = parseInt($(this).next().css('width').replace('px',''));
          if(containerwidth == 0){
            // A best-effort attempt to display in a special case - such as in a tab whose width isn't set yet, or something along those lines
            containerwidth = 500;
          }


          var speed = argsDiv.attr('data-speed');
          var titlecount = argsDiv.attr('data-titlecount');
          if(titlecount.indexOf('"') != -1){
            titlecount = parseInt(titlecount.replace(/"/g,''));
          } else {
            titlecount = parseInt(titlecount);
          }




          var transition = argsDiv.attr('data-transition');
          var coverwidth = argsDiv.attr('data-coverwidth');
          if(coverwidth.indexOf('"') != -1){
            coverwidth = parseInt(coverwidth.replace(/"/g,''));
          } else {
            coverwidth = parseInt(coverwidth);
          }

          var coverheight = argsDiv.attr('data-coverheight');
          if(coverheight.indexOf('"') != -1){
            coverheight = parseInt(coverheight.replace(/"/g,''));
          } else {
            coverheight = parseInt(coverheight);
          }

          var totalbookscount = $(this).next().find('.wpbooklist_carousel_entry_div').length;
          var totalwidth = (parseInt(coverwidth)+10)*totalbookscount;
          var neededwidth = titlecount*(coverwidth+20);

          function wpbooklist_carousel_fit_books(neededwidth, containerwidth, argsDiv, uniqueId){
            console.log($('#'+argsDiv[0].id))
            if(neededwidth <= containerwidth){
              $('#'+argsDiv[0].id).next().find('.wpbooklist_carousel_entry_div').each(function(index){
                //if($('#'+argsDiv[0].id) == 'wpbooklist-carousel-shortcode-atts-div-1'  ){

                  console.log('index'+index)
                  console.log('titlecount:'+(titlecount-1))
               // }
                if(index == titlecount-1){
                  var bookrightoffset = ($('#'+uniqueId).next().parent().width() - ($(this).offset().left + $(this).outerWidth()));
                  var containerrightoffset = ($('#'+uniqueId).next().parent().width() - ($('#'+argsDiv[0].id).next().find('#wpbooklist_carousel_main_display_div').offset().left + $('#'+argsDiv[0].id).next().outerWidth()));
                  var moving = (bookrightoffset-containerrightoffset);
                  containerwidth = containerwidth-moving+10;
                  console.log('Container Width:'+containerwidth)
                  $('#'+argsDiv[0].id).next().find('#wpbooklist_carousel_main_display_div').css({'width':containerwidth+'px'})
                }
              });
            } else{
              titlecount--;
              neededwidth = titlecount*(coverwidth+20);
              wpbooklist_carousel_fit_books(neededwidth, containerwidth, argsDiv, uniqueId);
            }
          }

          function wpbooklist_carousel_fit_books_right(neededwidth, containerwidth, argsDiv, uniqueId){

          console.log('fitbooksright neededwidth: '+neededwidth)
          console.log('fitbooksright containerwidth: '+containerwidth)

            if(neededwidth <= containerwidth){
              var dist_ho = Math.abs($('#'+argsDiv[0].id).next().find('.wpbooklist_carousel_entry_div').last().offset().left - $('#'+argsDiv[0].id).next().find('#wpbooklist-carousel-nav-div-right').offset().left);  // horizontal distance

              $('#'+argsDiv[0].id).next().find('.wpbooklist_carousel_entry_div').each(function(index){
                var right = parseInt($(this).css('right').replace('px',''))-dist_ho+coverwidth+20
                $(this).css({'right':right+'px'})
              });

/*

              $('#'+argsDiv[0].id).next().find('.wpbooklist_carousel_entry_div').each(function(index){
                if(index == totalbookscount-1){
                  var bookrightoffset = ($('#'+uniqueId).next().parent().width() - ($(this).offset().left + $(this).outerWidth()));
                  var containerrightoffset = ($('#'+uniqueId).next().parent().width() - ($('#'+argsDiv[0].id).next().offset().left + $('#'+argsDiv[0].id).next().outerWidth()));
                  var moving = (bookrightoffset-containerrightoffset);
                  //containerwidth = containerwidth-moving+10;







                  if(bookrightoffset > 0 && (bookrightoffset != containerrightoffset)){
                    console.log('tracker1')
                    $('#'+argsDiv[0].id).next().find('.wpbooklist_carousel_entry_div').each(function(index){
                      var right = parseInt($(this).css('right').replace('px',''))-(moving);
                      // for 3 books, this works
                      $(this).css({'right':(right+(coverwidth/2)-10)+'px'})

                      // for 2 books, this works
                      //$(this).css({'right':(right+coverwidth)+'px'})
                    });
                  }


                  if(bookrightoffset == containerrightoffset){
                    console.log('tracker2')
                    var bookleftoffset = $('#'+uniqueId).next().parent().width() - ($(this).offset().left);
                    var right = parseInt($(this).css('right').replace('px',''))+(bookrightoffset-bookleftoffset);
                    $('#'+argsDiv[0].id).next().find('.wpbooklist_carousel_entry_div').each(function(index){
                      $(this).css({'right':(right-40)+'px'})
                    });
                  }

                  if(bookrightoffset < 0){
                    console.log('tracker3')
                    var bookleftoffset = $('#'+uniqueId).next().parent().width() - ($(this).offset().left);
                    var right = parseInt($(this).css('right').replace('px',''))-(moving);
                    $('#'+argsDiv[0].id).next().find('.wpbooklist_carousel_entry_div').each(function(index){
                      //original
                      //$(this).css({'right':(right-40)+'px'})

                      // for 3 books, this works
                     // $(this).css({'right':(right+(coverwidth/2)-10)+'px'})

                      // for 2 books, this works
                      $(this).css({'right':(right+(coverwidth*2)+30)+'px'})
                    });
                  } 
            
                }
              });
*/ 
            } else{
              console.log('yo!!')
              titlecount--;
              neededwidth = titlecount*(coverwidth+20);
              wpbooklist_carousel_fit_books_right(neededwidth, containerwidth, argsDiv, uniqueId);
            }
          }

          // Call this function automatically as this will be the default
          wpbooklist_carousel_fit_books(neededwidth, containerwidth, argsDiv, uniqueId);

          if(transition == 'rotateright'){
            $(this).next().find('.wpbooklist_carousel_entry_div').each(function(index){
              $(this).css({'right':((totalbookscount * (parseInt(coverwidth)+20))-parseInt(containerwidth-coverwidth+(titlecount*20)))+coverwidth*(titlecount-1)+'px'});
            })
            // Call this function to align the books to the right
            wpbooklist_carousel_fit_books_right(neededwidth, containerwidth, argsDiv, uniqueId);
          }

          // Unhide the carousel
          $('.wpbooklist_carousel_main_display_div_class').animate({'opacity':'1'}, 1000)
          $('.wpbooklist-carousel-nav-arrow').animate({'opacity':'1'}, 1000)
          $('.wpbooklist-carousel-nav-arrow, .wpbooklist_carousel_inner_main_display_div .wpbooklist_cover_image_class').css({'pointer-events':'all'})


        });
      }

  });
  </script>
  <?php
}

// For Carousel rotation behavior
function carousel_rotation_javascript() { 
  ?>
    <script type="text/javascript" >
    "use strict";
    jQuery(document).ready(function($) {

      var interval;

      
      
      if($('#wpbooklist_carousel_main_display_div').css('width') != undefined){

        $('.wpbooklist-carousel-shortcode-atts-div-class').each(function(index){


          // Give a unique ID
          $(this).attr('id', 'wpbooklist-carousel-shortcode-atts-div'+'-'+index);
          var uniqueId = 'wpbooklist-carousel-shortcode-atts-div'+'-'+index;
          var spanIdModifier = index;


          var argsDiv = $(this);
          var table = argsDiv.attr('data-table');
          var width = parseInt(argsDiv.attr('data-width'));
          var containerwidth = parseInt($(this).next().css('width').replace('px',''));
          var speed = argsDiv.attr('data-speed');
          var titlecount = argsDiv.attr('data-titlecount');
          if(titlecount.indexOf('"') != -1){
            titlecount = parseInt(titlecount.replace(/"/g,''));
          } else {
            titlecount = parseInt(titlecount);
          }

          var transition = argsDiv.attr('data-transition');
          var coverwidth = argsDiv.attr('data-coverwidth');
          if(coverwidth.indexOf('"') != -1){
            coverwidth = parseInt(coverwidth.replace(/"/g,''));
          } else {
            coverwidth = parseInt(coverwidth);
          }

          var coverheight = argsDiv.attr('data-coverheight');
          if(coverheight.indexOf('"') != -1){
            coverheight = parseInt(coverheight.replace(/"/g,''));
          } else {
            coverheight = parseInt(coverheight);
          }
          var totalbookscount = $(this).next().find('.wpbooklist_carousel_entry_div').length;
          var totalwidth = (parseInt(coverwidth)+10)*totalbookscount;
          var neededwidth = titlecount*(coverwidth+20);
          var tracker = 0;

          var initialRight = parseInt($(this).next().find('.wpbooklist_carousel_entry_div').first().css('right').replace('px',''));

          var coverHoverPause = setInterval(function(){
            // Reset the carousel if it reaches the end
            if(tracker == totalbookscount-titlecount){
              console.log('REACHED END!')
              $('#'+argsDiv[0].id).next().find('.wpbooklist_carousel_entry_div').each(function(){
                $(this).animate({'right':initialRight+'px'})
              })
              tracker = 0;
              return;
            }

            // If we're rotating to the left...
            if(transition == 'rotateleft'){
              $('#'+argsDiv[0].id).next().find('.wpbooklist_carousel_entry_div').each(function(){
                var right = parseInt($(this).css('right').replace('px',''))+coverwidth+20;
                $(this).animate({'right':right+'px'})
              })
              tracker++;
              return;
            }

            // If we're rotating to the right...
            if(transition == 'rotateright'){
              $('#'+argsDiv[0].id).next().find('.wpbooklist_carousel_entry_div').each(function(){
                var right = parseInt($(this).css('right').replace('px',''))-coverwidth-20;
                $(this).animate({'right':right+'px'})
              })
              tracker++;
              return;
            }
          }, speed);

          $('#'+argsDiv[0].id).next().find('.wpbooklist_cover_image_class, .wpbooklist-carousel-nav-arrow').hover(function() {
            $(this).css({'opacity':'0.1'})
            if($(this).hasClass('wpbooklist_cover_image_class')){
              //$(this).next().css({'z-index':'999'})
            }
            // For pausing the rotation on hover of the book cover images
            clearInterval(coverHoverPause);
          },
          function() {
            $(this).css({'opacity':'1'})
            if($(this).hasClass('wpbooklist_cover_image_class')){
              //$(this).next().css({'z-index':'-1'})
            }
            // For restarting the rotation after hover of the book cover images
              coverHoverPause = setInterval(function(){
                // Reset the carousel if it reaches the end
                if(tracker == totalbookscount-titlecount){
                  $('#'+argsDiv[0].id).next().find('.wpbooklist_carousel_entry_div').each(function(){
                    $(this).animate({'right':initialRight+'px'})
                  })
                  tracker = 0;
                  return;
                }

                // If we're rotating to the left...
                if(transition == 'rotateleft'){
                  $('#'+argsDiv[0].id).next().find('.wpbooklist_carousel_entry_div').each(function(){
                    var right = parseInt($(this).css('right').replace('px',''))+coverwidth+20;
                    $(this).animate({'right':right+'px'})
                  })
                  tracker++;
                  return;
                }

                // If we're rotating to the right...
                if(transition == 'rotateright'){
                  $('#'+argsDiv[0].id).next().find('.wpbooklist_carousel_entry_div').each(function(){
                    var right = parseInt($(this).css('right').replace('px',''))-coverwidth-20;
                    $(this).animate({'right':right+'px'})
                  })
                  tracker++;
                  return;
                }
              }, speed);
          });

          
          //var myVar = setInterval(function(){ myTimer() }, 1000);
          //wpbooklist_carousel_rotate(initialRight, argsDiv, uniqueInterval);


          $('#'+argsDiv[0].id).next().find(".wpbooklist-carousel-nav-arrow").mouseup(function(event){

            clearInterval(coverHoverPause);
            //$(this).unbind('mouseenter mouseleave')

            if($(this).attr('id') == 'wpbooklist-carousel-nav-left'){


              // Reset the carousel if it reaches the end
              if(tracker == 0 || (tracker == totalbookscount-titlecount)){
                console.log('END!!')
                return;
              }



              console.log('left')
              $('#'+argsDiv[0].id).next().find(".wpbooklist-carousel-nav-arrow").css({'pointer-events':'none'})
              $('#'+argsDiv[0].id).next().find('.wpbooklist_carousel_entry_div').each(function(index){
                
                var right = parseInt($(this).css('right').replace('px',''))+coverwidth+20;

                if(index == totalbookscount-1){
                  $(this).animate({
                     right: right+'px'
                   },
                   {
                     easing: 'swing',
                     duration: 500,
                     complete: function(){
                        console.log('reassigned!')
                        $('#'+argsDiv[0].id).next().find(".wpbooklist-carousel-nav-arrow").css({'pointer-events':'all'})
                    }
                  });
                } else {
                  $(this).animate({'right':right+'px'}, 500)
                }
              })

              if(transition == 'rotateleft'){
                tracker++;
              } else {
                tracker--;
              }
            } else {

              // Reset the carousel if it reaches the end
              if(tracker == 0 || (tracker == totalbookscount-titlecount)){
                console.log('END!!')
                return;
              }

              console.log('right')
              $('#'+argsDiv[0].id).next().find(".wpbooklist-carousel-nav-arrow").css({'pointer-events':'none'})
              $('#'+argsDiv[0].id).next().find('.wpbooklist_carousel_entry_div').each(function(index){
                var right = parseInt($(this).css('right').replace('px',''))-coverwidth-20;
                if(index == totalbookscount-1){
                  $(this).animate({
                     right: right+'px'
                   },
                   {
                     easing: 'swing',
                     duration: 500,
                     complete: function(){
                        console.log('reassigned!')
                        $('#'+argsDiv[0].id).next().find(".wpbooklist-carousel-nav-arrow").css({'pointer-events':'all'})
                    }
                  });
                } else {
                  $(this).animate({'right':right+'px'}, 500)
                }
              })
              if(transition == 'rotateright'){
                tracker++;
              } else {
                tracker--;
              }
              return;

            }

            

            event.preventDefault ? event.preventDefault() : event.returnValue = false;
          });


          
            $('#'+argsDiv[0].id).next().find('.wpbooklist-carousel-title-span-class').each(function(index){

              
              /*
                var fontsize = parseInt($(this).css('font-size').replace('px',''))
                var newFontSize = parseInt( ((coverwidth/coverheight)*fontsize)+fontsize      );
                $(this).css({'font-size':newFontSize+'px'})
              */
              var textHeight = $(this).height();
              var origId = $(this).attr('id');
              $(this).attr('id', $(this).attr('id')+'-'+spanIdModifier);
              var id = $(this).attr('id')



              function wpbooklist_fit_text(textHeight, id){
                console.log('height')
                console.log(textHeight)




                console.log(coverheight)
                if(textHeight > (coverheight-10)){
                  console.log(id)
                  var lineClamp = $('#'+id).css('-webkit-line-clamp')

                  console.log('lineclamp')
                  console.log(lineClamp)

                  lineClamp = lineClamp-2
                  console.log('new lineclamp: '+lineClamp)
                  console.log($('#'+id))
                  $('#'+id).css({"-webkit-line-clamp":lineClamp.toString()})

                  textHeight = $('#'+id).height();
                  console.log('new textHeight: '+textHeight)
                  wpbooklist_fit_text(textHeight, id)
                } else {
                  var topBottomHeight = ((coverheight - textHeight)/2)+5
                  console.log('topBottomHeight')
                  console.log(topBottomHeight)
                  $('#'+id).css({"top":topBottomHeight+'px'})
                }
              }

              wpbooklist_fit_text(textHeight, id)
            });
          

          




        });

      }

    });
  </script>
  <?php
}


// For removing unneeded class names and span elements on each title, if using the 'action' shortcode argument.
function carousel_remove_junk_action_javascript() { 
  ?>
    <script type="text/javascript" >
    "use strict";
    jQuery(document).ready(function($) {
      setTimeout(function(){
        $('.wpbooklist_carousel_inner_main_display_div a').each(function(){
          $(this).removeClass('wpbooklist-show-book-colorbox');
          $(this).find('span').remove();
        })
      },2000)
  });
  </script>
  <?php
}

/*
 * Below is a boilerplate function with Javascript
 *
/*

// For 
add_action( 'admin_footer', 'carousel_boilerplate_javascript' );

function carousel_boilerplate_action_javascript() { 
  ?>
    <script type="text/javascript" >
    "use strict";
    jQuery(document).ready(function($) {
      $(document).on("click",".carousel-trigger-actions-checkbox", function(event){

        event.preventDefault ? event.preventDefault() : event.returnValue = false;
      });
  });
  </script>
  <?php
}
*/
?>