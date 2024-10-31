jQuery( document ).ready(
	function($){
		$( '.ssag-select-select2' ).select2();

		$( '.ssag-ads-count-btn' ).prop( 'disabled' , false );
		$( document ).on(
			'click' ,
			'.ssag-posts-restrictions-btn button' ,
			function(event){
				event.preventDefault();
				$( this ).closest( '.ssag-single-ads-main' ).find( '.ssag-posts-devices-btn' ).removeClass( 'ssag-posts-btn-active' );
				$( this ).closest( '.ssag-single-ads-main' ).find( '.ssag-posts-shortcode-btn' ).removeClass( 'ssag-posts-btn-active' );
				$( this ).closest( '.ssag-single-ads-main' ).find( '.ssag-posts-restrict-btn' ).removeClass( 'ssag-posts-btn-active' );
				$( this ).closest( '.ssag-single-ads-main' ).find( '.ssag-posts-devices-options' ).hide();
				$( this ).closest( '.ssag-single-ads-main' ).find( '.ssag-posts-shortcode-options' ).hide();
				$( this ).closest( '.ssag-single-ads-main' ).find( '.ssag-posts-restrict-options' ).hide();
				// console.log( $(this).text() + ' is clicked' );
				$( this ).addClass( 'ssag-posts-btn-active' );
				$( this ).closest( '.ssag-single-ads-main' ).find( '.' + $( this ).data( 'value' ) ).show();
			}
		);

		$( document ).on(
			'click' ,
			'body' ,
			function(){
				$( '.ssag-save-post-messages' ).html( '' );
			}
		);
		$( document ).on(
			'click' ,
			'.ssag-menu-item-btn' ,
			function(){
				$( '.ssag-menu-item' ).removeClass( 'ssag-menu-item-active' );
				$( this ).addClass( 'ssag-menu-item-active' );
				$( '.ssag-all-ads-main' ).addClass( 'ssag-all-ads-hidden' );
				$( '.' + $( this ).data( 'div' ) ).removeClass( 'ssag-all-ads-hidden' );
			}
		);

		$( document ).on(
			'change' ,
			'[name="ssag_ads_align"]' ,
			function(){
				if ( $( this ).val() == 'custom-css' ) {
					$( $( this ).closest( 'form' ).find( '.ssag-this-ad-custom-css' )[0] ).removeClass( 'ssag-hidden' );
					$( $( this ).closest( 'form' ).find( '.ssag-this-ad-custom-css' )[0] ).show();
				} else {
					$( $( this ).closest( 'form' ).find( '.ssag-this-ad-custom-css' )[0] ).addClass( 'ssag-hidden' );
					$( $( this ).closest( 'form' ).find( '.ssag-this-ad-custom-css' )[0] ).hide();
				}
			}
		);

		$( document ).on(
			'change' ,
			'.ssag_ads_insertion' ,
			function(){
				console.log( 'on change' );
				console.log( $( this ).val() );
				if ( ($( this ).val() == 'before-para') || ( $( this ).val() == 'after-para' ) || ( $( this ).val() == 'before-img' ) || ( $( this ).val() == 'after-img' ) || ( $( this ).val() == 'between-posts' ) ) {
					  $( this ).closest( '.ssag-posts-insertion-div' ).find( '.ssag_ads_para_id-main' ).removeClass( 'ssag-hidden' );
					  $( this ).closest( '.ssag-posts-insertion-div' ).find( '.ssag_ads_para_id-main' ).show();
				} else {
					 $( this ).closest( '.ssag-posts-insertion-div' ).find( '.ssag_ads_para_id-main' ).addClass( 'ssag-hidden' );
					 $( this ).closest( '.ssag-posts-insertion-div' ).find( '.ssag_ads_para_id-main' ).hide();
				}
			}
		);

		$( document ).on(
			'click' ,
			'.ssag-ads-count-btn-add-new' ,
			function(){
				// console.log('Add new add clicked');
				old_ads_exists = $( this ).data( 'ads_exists' );
				$( $( this ).closest( '.ssag-ads-btn-main' ).find( '.ssag-ads-count-btn-' + old_ads_exists )[0] ).removeClass( 'ssag-hidden' );
				$( $( this ).closest( '.ssag-ads-btn-main' ).find( '.ssag-ads-count-btn-' + old_ads_exists )[0] ).click();
				new_ads_exists = ++old_ads_exists;
				if ( old_ads_exists > 23 ) {
					  $( this ).remove();
				}
				$( this ).data( 'ads_exists' , new_ads_exists );
				ads_exists = $( this ).data( 'ads_exists' );
				// console.log('new is => ' + ads_exists );
			}
		);

		$( document ).on(
			'click' ,
			'.ssag-ads-count-btn' ,
			function(){
				// console.log('View Ad add clicked');
				$( '.ssag-ads-count-btn' ).removeClass( 'ssag-ads-count-btn-active' );
				$( this ).addClass( 'ssag-ads-count-btn-active' );
				$( this ).closest( '.ssag-all-ads-main' ).find( '.ssag-single-ads-main' ).hide();
				$( this ).closest( '.ssag-all-ads-main' ).find( '.ssag-single-ads-main' ).addClass( 'ssag-hidden' );
				$( this ).closest( '.ssag-all-ads-main' ).find( '.' + $( this ).data( 'options' ) ).show();
				$( this ).closest( '.ssag-all-ads-main' ).find( '.' + $( this ).data( 'options' ) ).removeClass( 'ssag-hidden' );
			}
		);

		$( document ).on(
			'submit' ,
			'.ssag-form-header' ,
			function(event){
				event.preventDefault();
				// console.log( $('[name="ssag_styles_for_header"]').val() );
				$.ajax(
					{
						url: ajaxurl,
						type: 'POST',
						data: {
							action : 'ssag_save_header',
							nonce: ssag_object.nonce,
							ssag_styles_for_header: $( '[name="ssag_styles_for_header"]' ).val(),
							ssag_script_for_footer: $( '[name="ssag_script_for_footer"]' ).val(),
						},
						success: function(data){
							console.log( 'success' );
							console.log( data );
							if (data.message) {
								$( '.ssag-save-post-messages' ).html( data.message );
								$( "html, body" ).animate( { scrollTop: 0 }, "slow" );
							}
						},
						error: function (error) {
							console.log( 'errors' );
							console.log( error );
						}
					}
				);
			}
		);
		$( document ).on(
			'submit' ,
			'.ssag-support-form' ,
			function(event){
				event.preventDefault();
				$.ajax(
					{
						url: ajaxurl,
						type: 'POST',
						data: {
							action : 'ssag_support',
							nonce: ssag_object.nonce,
							ssag_support_fname: $( '[name="ssag_support_fname"]' ).val(),
							ssag_support_lname: $( '[name="ssag_support_lname"]' ).val(),
							ssag_support_email: $( '[name="ssag_support_email"]' ).val(),
							ssag_support_website_url: $( '[name="ssag_support_website_url"]' ).val(),
							ssag_support_message: $( '[name="ssag_support_message"]' ).val(),
						},
						success: function(data){
							console.log( 'success' );
							console.log( data );
							if (data.message) {
								$( '.ssag-save-post-messages' ).html( data.message );
								$( "html, body" ).animate( { scrollTop: 0 }, "slow" );
							}
						},
						error: function (error) {
							console.log( 'errors' );
							console.log( error );
						}
					}
				);
			}
		);

		$( document ).on(
			'click' ,
			'.ssag-upload-ads-txt' ,
			function(){
				$( '.ssag-upload-ads-txt-input' ).click();
			}
		);

		$( document ).on(
			'change' ,
			'.ssag-upload-ads-txt-input' ,
			function(){
				var file_reading    = new FileReader();
				file_reading.onload = function(){
					var all_string_code = file_reading.result;
					$( '[name="ssag_ads_txt_all_code"]' ).val( all_string_code );
				}
				file_reading.readAsText( this.files[0] );
				$( '.ssag-upload-ads-txt-input' ).val( '' );
			}
		);

		$( document ).on(
			'submit' ,
			'.ssag-form-ads-txt' ,
			function(event){
				event.preventDefault();
				$.ajax(
					{
						url: ajaxurl,
						type: 'POST',
						data: {
							action : 'ssag_save_ads_txt',
							nonce: ssag_object.nonce,
							ssag_ads_txt_all_code: $( '[name="ssag_ads_txt_all_code"]' ).val(),
						},
						success: function(data){
							console.log( 'success' );
							console.log( data );
							if (data.message) {
								$( '.ssag-save-post-messages' ).html( data.message );
								$( "html, body" ).animate( { scrollTop: 0 }, "slow" );
							}
						},
						error: function (error) {
							console.log( 'errors' );
							console.log( error );
						}
					}
				);
			}
		);

		$( document ).on(
			'click' ,
			'.ssag-ads-save-this-ads' ,
			function(){
				$( '.ssag-ads-save-this-ads-spin' ).addClass( 'fa fa-spin' );
				$( '.ssag-ads-save-this-ads-spin' ).closest( 'button' ).prop( 'disabled' , true );
				$( '.ssag-ads-save-this-ads-spin' ).closest( 'button' ).css( 'opacity' , '0.5' );
				$( '.ssag-ads-form:visible' ).submit();

			}
		);

		$( document ).on(
			'submit' ,
			'.ssag-ads-form' ,
			function(event){
				// const form_data = new FormData(this);
				event.preventDefault();
				this_form               = this;
				all_form_data           = $( this ).serialize();
				form_data               = new Array();
				this_form_submit_button = $( this ).find( '.ssag-ads-save-this-ads-spin' )[0];
				$( this_form_submit_button ).addClass( 'fa fa-spin' );
				$( this_form_submit_button ).closest( 'button' ).prop( 'disabled' , true );
				$( this_form_submit_button ).closest( 'button' ).css( 'opacity' , '0.5' );

				// console.log( 'Value is -> ' + $( $( this_form ).find( '[name="ssag_ads_insertion"]' )[0] ).val()  );
				// console.log( 'btn Class is -> ' + $(this_form).closest('.ssag-single-ads-main').data('active_btn') );
				// console.log('pages are ')
				// console.log( $( $( this ).find( '[name="ssag_ads_posts_ids"]' )[0] ).val() );

				$.ajax(
					{
						url: ajaxurl,
						type: 'POST',
						data: {
							action : 'ssag_save_single_add',
							// all_data: all_form_data,
							// all_data: form_data,
							ssag_ads_id:  $( $( this ).find( '[name="ssag_ads_id"]' )[0] ).val(),
							ssag_ads_title:  $( $( this ).find( '[name="ssag_ads_title"]' )[0] ).val(),
							ssag_ads_content_html:  $( $( this ).find( '[name="ssag_ads_content_html"]' )[0] ).val(),
							ssag_ads_include_posts:  $( $( this ).find( '[name="ssag_ads_include_posts"]' )[0] ).is( ':checked' ) ? 'yes' : 'no',
							ssag_ads_include_pages:  $( $( this ).find( '[name="ssag_ads_include_pages"]' )[0] ).is( ':checked' ) ? 'yes' : 'no',
							ssag_ads_include_home_page:  $( $( this ).find( '[name="ssag_ads_include_home_page"]' )[0] ).is( ':checked' ) ? 'yes' : 'no',
							ssag_ads_include_search_pages:  $( $( this ).find( '[name="ssag_ads_include_search_pages"]' )[0] ).is( ':checked' ) ? 'yes' : 'no',
							ssag_ads_include_cat_pages:  $( $( this ).find( '[name="ssag_ads_include_cat_pages"]' )[0] ).is( ':checked' ) ? 'yes' : 'no',
							ssag_ads_include_arch_pages:  $( $( this ).find( '[name="ssag_ads_include_arch_pages"]' )[0] ).is( ':checked' ) ? 'yes' : 'no',
							ssag_ads_insertion:  $( $( this ).find( '[name="ssag_ads_insertion"]' )[0] ).val(),
							ssag_ads_para_id:  $( $( this ).find( '[name="ssag_ads_para_id"]' )[0] ).val(),
							ssag_ads_align:  $( $( this ).find( '[name="ssag_ads_align"]' )[0] ).val(), // ssag_ad_custom_css
							ssag_ad_custom_css:  $( $( this ).find( '[name="ssag_ad_custom_css"]' )[0] ).val(), // ssag_ad_custom_css
							ssag_ads_categories:  $( $( this ).find( '[name="ssag_ads_categories"]' )[0] ).val(),
							ssag_ads_tags:  $( $( this ).find( '[name="ssag_ads_tags"]' )[0] ).val(),
							ssag_ads_posts_ids:  $( $( this ).find( '[name="ssag_ads_posts_ids"]' )[0] ).val(),
							ssag_ads_url:  $( $( this ).find( '[name="ssag_ads_url"]' )[0] ).val(),
							ssag_ads_url_parameter:  $( $( this ).find( '[name="ssag_ads_url_parameter"]' )[0] ).val(),
							ssag_ads_referrers:  $( $( this ).find( '[name="ssag_ads_referrers"]' )[0] ).val(),
							ssag_ads_clients:  $( $( this ).find( '[name="ssag_ads_clients"]' )[0] ).val(),
							ssag_ads_show_on_widget:  $( $( this ).find( '[name="ssag_ads_show_on_widget"]' )[0] ).is( ':checked' ) ? 'yes' : 'no',
							ssag_ads_show_on_shortcode:  $( $( this ).find( '[name="ssag_ads_show_on_shortcode"]' )[0] ).is( ':checked' ) ? 'yes' : 'no',
							ssag_ads_show_on_php_function:  $( $( this ).find( '[name="ssag_ads_show_on_php_function"]' )[0] ).is( ':checked' ) ? 'yes' : 'no',
							ssag_ads_use_devices:  $( $( this ).find( '[name="ssag_ads_use_devices"]' )[0] ).is( ':checked' ) ? 'yes' : 'no',
							ssag_ads_use_desktop:  $( $( this ).find( '[name="ssag_ads_use_desktop"]' )[0] ).is( ':checked' ) ? 'yes' : 'no',
							ssag_ads_use_tablet:  $( $( this ).find( '[name="ssag_ads_use_tablet"]' )[0] ).is( ':checked' ) ? 'yes' : 'no',
							ssag_ads_use_phone:  $( $( this ).find( '[name="ssag_ads_use_phone"]' )[0] ).is( ':checked' ) ? 'yes' : 'no',

						},
						// dataType: 'json',
						// cache : false,
						// processData: true,
						// processData: false,
						// contentType: false,
						success: function(data){
							console.log( 'success' );
							console.log( data );
							if (data.message) {
								$( '.ssag-save-post-messages' ).html( data.message );
								$( "html, body" ).animate( { scrollTop: 0 }, "slow" );
							}
							// console.log( 'Value is -> ' + $( $( this_form ).find( '[name="ssag_ads_insertion"]' )[0] ).val()  );
							// console.log( 'btn Class is -> ' + $(this_form).closest('.ssag-single-ads-main').data('active_btn') );
							if ( 'disable' == $( $( this_form ).find( '[name="ssag_ads_insertion"]' )[0] ).val() ) {
								$( '.' + $( this_form ).closest( '.ssag-single-ads-main' ).data( 'active_btn' ) ).removeClass( 'ssag-ads-menu-saved' );
							} else {
								$( '.' + $( this_form ).closest( '.ssag-single-ads-main' ).data( 'active_btn' ) ).addClass( 'ssag-ads-menu-saved' );
							}

							$( this_form_submit_button ).removeClass( 'fa fa-spin' );
							$( this_form_submit_button ).closest( 'button' ).prop( 'disabled' , false );
							$( this_form_submit_button ).closest( 'button' ).css( 'opacity' , '1' );

							$( '.ssag-ads-save-this-ads-spin' ).removeClass( 'fa fa-spin' );
							$( '.ssag-ads-save-this-ads-spin' ).closest( 'button' ).prop( 'disabled' , false );
							$( '.ssag-ads-save-this-ads-spin' ).closest( 'button' ).css( 'opacity' , '1' );

						},
						error: function (error) {
							// console.log( 'errors' );
							// console.log( error );

							message  = '<div class="success-msg">';
							message += '<i class="fa fa-times-circle"></i>';
							message += 'Your Ad is successfully saved';
							message += '</div>';
							$( '.ssag-save-post-messages' ).html( message );
							$( "html, body" ).animate( { scrollTop: 0 }, "slow" );

							// console.log( 'Value is -> ' + $( $( this_form ).find( '[name="ssag_ads_insertion"]' )[0] ).val()  );
							// console.log( 'btn Class is -> ' + $(this_form).closest('.ssag-single-ads-main').data('active_btn') );
							if ( 'disable' == $( $( this_form ).find( '[name="ssag_ads_insertion"]' )[0] ).val() ) {
								$( '.' + $( this_form ).closest( '.ssag-single-ads-main' ).data( 'active_btn' ) ).removeClass( 'ssag-ads-menu-saved' );
							} else {
								$( '.' + $( this_form ).closest( '.ssag-single-ads-main' ).data( 'active_btn' ) ).addClass( 'ssag-ads-menu-saved' );
							}

							$( this_form_submit_button ).removeClass( 'fa fa-spin' );
							$( this_form_submit_button ).closest( 'button' ).prop( 'disabled' , false );
							$( this_form_submit_button ).closest( 'button' ).css( 'opacity' , '1' );

							$( '.ssag-ads-save-this-ads-spin' ).removeClass( 'fa fa-spin' );
							$( '.ssag-ads-save-this-ads-spin' ).closest( 'button' ).prop( 'disabled' , false );
							$( '.ssag-ads-save-this-ads-spin' ).closest( 'button' ).css( 'opacity' , '1' );
						}
					}
				);
				// all_form_data_arr_1 = all_form_data.split('&');
				// for (let index = 0; index < all_form_data_arr_1.length; index++) {
				// const element = all_form_data_arr_1[index];
				// all_form_data_arr_2 = element.split('&');
				// console.log('Key is => ' + all_form_data_arr_2[0]  );
				// console.log('value is ' );
				// console.log(all_form_data_arr_2[1]);
				// console.log('------------------------');
				// }
			}
		);

		// $(document).on( 'click' , '.upload_image_for_component' , function(){
		// var image_class = $(this).data('image');
		// var attachment_id = $(this).data('attachment_id');
		// if (this.window === undefined) {
		// this.window = wp.media({
		// title: $(this).data('text'),
		// library: {type: 'image'},
		// multiple: false,
		// button: {text: $(this).data('text')}
		// });

		// var self = this;
		// this.window.on('select', function() {
		// var response = self.window.state().get('selection').first().toJSON();
		// $( attachment_id ).val(response.id);
		// $( image_class ).attr('src', response.url);
		// $( image_class ).attr('width', '200' );
		// $( image_class ).attr('height', '200' );
		// });
		// }
		// this.window.open();
		// return false;
		// });
	}
);
