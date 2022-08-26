<?php

  function addBookMetaBox(){
    add_meta_box(//Must be in the below order
  'license_select_box',//ID for the Box
  'Book Attributes',//Title:what will show in the top of the box
  'licenseSelectMetaBoxCreator',//Callback: Method called that contains what's inside the box
  'book',//Screen - post types that this appears on
  'side',//where it appears
  'high',//priority of where the box appears (high or low)
  null//Callback args: provides arguments to callback function
);
}
add_action('add_meta_boxes','addBookMetaBox');

function licenseSelectMetaBoxCreator($post){
?>
  <style type="text/css">
  #bookMetaBox{
    display:flex;
    flex-direction: column;
    align-items: left;
  }

  #bookMetaBox > div{
    padding:5px;
  }

  #bookMetaBox > div > p{
    margin-top:5px;
    margin-bottom:0px;
  }
  #bookMetaBox > div > textarea{
    width:100%;
    height: 100px;
  }
  </style>
  <?php
    wp_nonce_field( 'licenseSelectMetaBox', 'licenseSelectMetaBox-nonce' );
    $allBooks = getTopLevelBooks();
    if (in_array($post, $allBooks)) {
        $value = get_post_meta( $post->ID, 'bookLicense', true );
        if ($value == null){
            $value = 'allrightsreserved';
            //consolePrint('License for this book: '.$value);
        }

        //Add metabox for CC Licenses
        $licenseArray = array(
            'allrightsreserved',
            'by',
            'by-sa',
            'by-nc',
            'by-nc-sa',
            'by-nd',
            'by-nc-nd',
            'cc-zero',
        );?>
        <div id="bookMetaBox">
          <div>
            <p>Select a License: </p>
            <!-- <label for="licenseSelector">Select a License for this Book: :</label> -->
            <?php

            echo '<select id="licenseSelector" name="licenseSelector">';
            foreach($licenseArray as $CCLicense){
           // echo '<tr><td>';
            //consolePrint('Checking '.$CCLicense.' and '.$value);

            $isChecked = '';
            if(strcmp($CCLicense, $value) == 0)
            {
                $isChecked = 'selected';
            }
            if ($CCLicense == 'allrightsreserved'){
                echo '<option value="'.$CCLicense.'" '.$isChecked.'>All Rights Reserved</option>';
                //echo '<input type="radio" name="licenseSelector" value="'.$CCLicense.'" '.$isChecked.'>All Rights Reserved</input>';
            }
            else{
                $CCimage = esc_url(get_template_directory_uri()).'/inc/images/'.$CCLicense.'.png';
                $CCDescription = '<a target="_blank" href="https://creativecommons.org/licenses/'.$CCLicense.'/4.0/">CC '.strtoupper ($CCLicense).'</a>';
                $CCImageTag = '<img style="width:30%; height:auto;" src="'.$CCimage.'" />';
                //echo '<input style="position: relative; bottom: 0.5em;" type="radio" name="licenseSelector" value="'.$CCLicense.'" '.$isChecked.'>'.$CCImageTag.' '.$CCDescription;
                echo '<option style="background-image:url('.$CCimage.');" value="'.$CCLicense.'" '.$isChecked.'>'.$CCDescription.'</option>';
            }


        }?>
        </select>
      </div>
        <!--- ALLOW FEEDBACK --->

        <div>
          <?php $feedbackOn = get_post_meta( $post->ID, 'acceptFeedback', true );
            if($feedbackOn == true)
            {

                $isChecked = 'checked';
            }


          echo '<input type="checkbox" id="acceptFeedback" name="acceptFeedback" '.$isChecked.'> Allow Voting';

          ?>
     </div>
        <!-- FOOTER TEXT -->
      <div>
            <p>Custom Footer HTML</p>

          <?php $footerText = get_post_meta( $post->ID, 'footerText', true );
            if($footerText){
              echo '<textarea id="footerText" name="footerText" value="">'.$footerText.'</textarea>';
            }
            else{
              echo '<textarea rows="10"  id="footerText" name="footerText" >Embedded videos, credited images / media are not inclusive of this license, so please check with the original creators if you wish to use them.</textarea>';
            }
          ?>
          <p style="font-size:0.8em;">Default Text: Embedded videos, credited images / media are not inclusive of this license, so please check with the original creators if you wish to use them.</p>
          </div>
        </div>

     <?php
    }
    else{
        echo('This is a chapter of a Book. To select a license go to a top of the hierarchy - the parent of this chapter to add a license.');
        ?>
    <?php
    }
    // Don't forget about this, otherwise you will mess up with other data on the book
    wp_reset_postdata();
  }

  function saveMeta( $post_id ) {
    // Check if our nonce is set.
    if ( !isset( $_POST['licenseSelectMetaBox-nonce'] ) ) {
            return;
    }
    // Verify that the nonce is valid.
    if ( !wp_verify_nonce( $_POST['licenseSelectMetaBox-nonce'], 'licenseSelectMetaBox' ) ) {
            return;
    }
    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
    }
    // Check the user's permissions.
    if ( !current_user_can( 'edit_post', $post_id ) ) {
            return;
    }
    // Sanitize user input.
    $newLicense = ( isset( $_POST['licenseSelector'] ) ? sanitize_html_class( $_POST['licenseSelector'] ) : '' );
    update_post_meta( $post_id, 'bookLicense', $newLicense );

   // Checks for input and saves
   if( isset( $_POST[ 'acceptFeedback' ]))  {
      update_post_meta( $post_id, 'acceptFeedback', $_POST['acceptFeedback']);
  } else{
    delete_post_meta( $post_id, 'acceptFeedback');
  }


  // Checks for input and saves if needed
  if( isset( $_POST[ 'footerText' ] ) ) {
      update_post_meta( $post_id, 'footerText', $_POST[ 'footerText' ] );
  }

}
add_action( 'save_post', 'saveMeta' );


/* --------------- ADD CUSTOM COLUMNS TO CHAPTERS --------------- */
add_filter( 'manage_book_posts_columns', 'addColumnsToParts' );
function addColumnsToParts($columns) {
    //unset( $columns['author'] );//Gets rid of this Column! YIKES!
    unset( $columns['comments'] );//Gets rid of this Column! YIKES!
    $new = array();
  foreach($columns as $key => $title) {
    if ($key=='date') {// Put the Thumbnail column before the Author column
      $new['subject'] = 'Subjects';
      $new['order'] = 'Order';
      $new['votes'] = 'Votes';
      $new['license'] = 'License';

    }
    $new[$key] = $title;
  }
  return $new;
}

// Add the data to the custom columns for book type:
  add_action( 'manage_book_posts_custom_column' , 'custom_page_column', 10, 2 );
  function custom_page_column( $column, $post_id ) {
      $allBooks = getTopLevelBooks();
      $thePage = get_post($post_id);
          switch ( $column ) {
              case 'order' :
                $thisOrder = get_post($post_id);
                echo $thisOrder->menu_order;
                break;
              case 'votes' :
                $thisPost = get_post($post_id);
                $bookRoot = getBookfromChapter($thisPost);
	              $root = get_post($bookRoot);
                $feedbackOn = get_post_meta( $root->ID, 'acceptFeedback', true );
                if($feedbackOn == true){
                  $voteData = getVoteData($post_id);
                  if ($voteData){
                    $fontAwesome = esc_url(get_template_directory_uri()).'/css/all.css';
      ?>
                  <link rel="stylesheet" href="<?php echo $fontAwesome; ?>">
                  <?php

                  $totalCount = $voteData[0] + $voteData[1];
                  $percentage = $voteData[0]/$totalCount;
                  $percentageValue =  round($percentage,2)*100;
                   echo '<p style="text-align:center;  margin-bottom:2px;">'.$voteData[0].' <i class="far fa-thumbs-up"></i> - '.$voteData[1].' <i class="far fa-thumbs-down"></i> - ('.$totalCount.')';

                    echo '<div id="vote-chart" style="padding-left:10px; width:100%; height:10px; border-radius:20px;background: rgb(220,112,108);
                    background: linear-gradient(90deg, rgba(103,216,173,1)'.$percentageValue.'%,rgba(220,112,108,1)'.$percentageValue.'%);">';
                   ?>
                        </div>
                          <?php
                  }
                  else{
                    echo 'No vote data yet.';
                  }
                }
                else{
                  echo 'Voting not enabled. Edit top level book to enable.';
                }
                break;
              case 'subject' :
                $subjects = get_the_terms( $post->ID, 'subject' );
                if ($subjects){
                  $i = 1;
                  foreach ( $subjects as $term ) {
                      $term_link = get_term_link( $term, array( 'subjects', 'type' ) );
                          if( is_wp_error( $term_link ) )
                          continue;
                          //echo '<a href="' . $term_link . '">' . $term->name . '</a>';
                          echo $term->name;
                          //  Add comma (except after the last theme)
                          echo ($i < count($subjects))? ", " : "";
                          // Increment counter
                          $i++;
                  }
                }

                break;
              case 'license' :
                  if (in_array($thePage, $allBooks)) {
                      $CCLicense = get_post_meta( $post_id, 'bookLicense', true );
                      if ($CCLicense == 'allrightsreserved'){
                          echo 'All Rights Reserved &copy; ';
                          echo the_modified_time('Y').'</p>';
                      }
                      else if (!$CCLicense){
                        echo 'No License Chosen';
                      }
                      else{
                          $CCLink = 'https://creativecommons.org/licenses/'.$CCLicense.'/4.0/';
                          $CCimage = '../images/'.$CCLicense.'.png';
                          echo '<a target="_blank" href="'.$CCLink.'"><img style="height:30px; width:auto; padding-top:5px;" src="'.esc_url(plugin_dir_url(__FILE__)).$CCimage.'"/></a>';
                      }

                  }
                  break;


          }
  }

/* --------------- ADD FILTER TO PAGES --------------- */
function getTopLevelBooks(){
	$query_args = array('parent' => 0, // required
	'post_type' => 'book', // required
	'sort_order'   => 'ASC',
	'sort_column'  => 'menu_order',
);
	return get_pages( $query_args );
}

function getBookfromChapter($thisChapter){//gets book for the current page.
	$bookRoot = new stdClass();
	$thisPage = get_post($thisChapter->ID);
	if ($thisPage->post_parent)	{
		$ancestors=get_post_ancestors($thisChapter->ID);
		$root=count($ancestors)-1;
		$bookRoot = $ancestors[$root];
	} else {
		$bookRoot = $thisChapter;
	}
	return $bookRoot;
}

function getChapters($forBook){

	$args = array(
		'posts_per_page' => 0,
		'order'          => 'ASC',
		'orderby'		=>'menu_order',
		'post_parent'    => $forBook,
		'post_status'    => null,
		'post_type'      => 'book',
	);
	return get_children( $args );
}
function getVoteData($post_id){
	$upvotes = 0;
	$downvotes = 0;
	$post_votes = get_post_meta($post_id,'updown_votes',false);
	if ($post_votes){
		foreach($post_votes as $vote){
			if ($vote == '+'){
				$upvotes++;
			}
			else if ($vote == '-'){
				$downvotes++;
			}
		}
		return array($upvotes,$downvotes);
	}
	else{
		return null;
	}
}
add_action( 'restrict_manage_posts', 'filterBookList' );

function filterBookList($post_type){
    $type = 'book';
    if (isset($_GET['post_type'])) {
        $type = $_GET['post_type'];
    }

    //only add filter to post type you want
    if ('book' == $post_type){
        //get all the books
        $allBooks = get_posts([
          'post_type' => 'book',
          'post_status' => array('publish', 'pending', 'draft', 'auto-draft', 'future'),
          'numberposts' => -1,
          'order'    => 'ASC'
        ]);

        $allBooks = getTopLevelBooks()
        ?>
        <select name="bookSelector">
        <option value=""><?php _e('All Books', 'theopenbook'); ?></option>
        <?php
            $currentBook = isset($_GET['bookSelector'])? $_GET['bookSelector']:'';
            foreach ($allBooks as $thisBook) {
              $bookTitle = get_the_title($thisBook);
              $thisBookID = $thisBook->ID;
                printf
                    (
                        '<option value="%s"%s>%s</option>',
                        $thisBookID,
                        $thisBookID == $currentBook? ' selected="selected"':'',
                        $bookTitle
                    );
                }
        ?>
        </select>
        <?php
    }
}

function find_descendants($post_id) {
  $descendant_ids = array();
  array_push($descendant_ids, $post_id);//Add the main book to show that
  $args = array(
        'post_type' => 'book', // required
        'post_status' => array('draft', 'publish','future'),
        'child_of' => $post_id,
        'posts_per_page' => -1,
      );
  $pages = get_pages($args);

  foreach ($pages as $page) {

    array_push($descendant_ids, $page->ID); }
  return $descendant_ids;
}

function SearchFilter($query) {
  global $pagenow;
  $type = 'book';
  if (isset($_GET['post_type'])) {
      $type = $_GET['post_type'];
  }
  if ( 'book' == $type && is_admin()
      && $pagenow=='edit.php'
      && isset($_GET['bookSelector'])
      && $_GET['bookSelector'] != ''
      && $query->is_main_query()
      ) {
  if ($query->is_search) {
    $selectedBook = $_GET['bookSelector'];
    //consolePrint('The book:'.$selectedBook);
      $query->set ( 'post__in', find_descendants($selectedBook) );
  }
  }
  return $query;
}
add_filter('pre_get_posts','SearchFilter');
//Adds text above title
add_action( 'load-edit.php', function(){
    $screen = get_current_screen();
     // Only edit post screen:
    if( 'edit-book' === $screen->id )
    {
         // Before:
         add_action( 'all_admin_notices', function(){
             echo '<p>Recommended Plugins to make life easier: <a href="https://wordpress.org/plugins/simple-page-ordering/" target="_blank">Simple Page Ordering</a>  |  <a href="https://wordpress.org/plugins/broken-link-checker/" target="_blank">Broken Link Checker</a></p>';// |  <a href="https://wordpress.org/plugins/publishpress-authors/" target="_blank">Publish Press for multiple authors</a></p>';
         });

         // After:
        //  add_action( 'in_admin_footer', function(){
        //      echo '<p>Goodbye from <strong>in_admin_footer</strong>!</p>';
        //  });
     }
 });
?>