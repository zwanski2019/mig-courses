<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue Media Scripts
 */
wp_enqueue_media();
?>
<div class="academy-extend-user-profile-wrap">
	<h2><?php esc_html_e( 'Academy Info', 'academy' ); ?></h2>
	<table class="form-table">
		<tr class="user-designation-wrap">
			<th><label for="academy_profile_designation"><?php esc_html_e( 'Profile Designation', 'academy' ); ?></label></th>
			<td>
				<input type="text" name="academy_profile_designation" id="academy_profile_designation" value="<?php echo esc_attr( get_user_meta( $user->ID, 'academy_profile_designation', true ) ); ?>" class="regular-text" />
			</td>
		</tr>
		<tr class="user-description-wrap">
			<th><label for="description"><?php esc_html_e( 'Profile Bio', 'academy' ); ?></label></th>
			<td>
				<?php
				wp_editor(
					get_user_meta( $user->ID, 'academy_profile_bio', true ),
					'academy_profile_bio',
					array(
						'teeny'         => true,
						'media_buttons' => false,
						'quicktags'     => false,
						'editor_height' => 200,
					)
				);
				?>
				<p class="description"><?php esc_html_e( 'Share a little biographical information to fill out your profile. This may be shown publicly.', 'academy' ); ?></p>
			</td>
		</tr>

		<tr class="user-photo-wrap">
			<th><label for="description"><?php esc_html_e( 'Profile Photo', 'academy' ); ?></label></th>
			<td>
				<div class="video-wrap">
					<p class="video-img" style="max-width: 300px;">
						<?php
							$profile_photo = get_user_meta( $user->ID, 'academy_profile_photo', true );
						if ( $profile_photo ) {
							echo '<img src="' . esc_url( $profile_photo ) . '" alt="" style="max-width:100%" /> ';
						}
						?>
					</p>
					<input type="hidden" id="academy_profile_photo" name="academy_profile_photo" value="<?php echo esc_attr( $profile_photo ); ?>">
					<button type="button" class="academy_profile_photo_remove_btn button button-primary <?php echo( empty( $profile_photo ) ? 'hidden' : '' ); ?>"><?php esc_html_e( 'Remove this image', 'academy' ); ?></button>
					<button type="button" class="academy_profile_photo_upload_btn button button-primary <?php echo( ! empty( $profile_photo ) ? 'hidden' : '' ); ?>"><?php esc_html_e( 'Upload', 'academy' ); ?></button>
				</div>
			</td>
		</tr>
	</table>
</div>

