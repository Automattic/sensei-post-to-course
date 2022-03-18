document.addEventListener( 'DOMContentLoaded', ( event ) => {
	const category = document.getElementsByClassName( 'sptc-category' );
	const postType = document.getElementById( 'sptc-post-type' );

	postType.addEventListener( 'change', event => {
		if ( 'post' === postType.value ) {
			category[0].style.display = 'flex';
		} else {
			category[0].style.display = 'none';
		}
	} );
} );
