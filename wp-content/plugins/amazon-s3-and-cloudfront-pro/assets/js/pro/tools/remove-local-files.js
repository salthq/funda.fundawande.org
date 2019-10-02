(function( $, as3cfModal ) {

	as3cfpro.Tools = as3cfpro.Tools ? as3cfpro.Tools : {};

	/**
	 * The object that handles the remove local files tool.
	 */
	as3cfpro.Tools.RemoveLocalFiles = {

		/**
		 * Store our settings form.
		 *
		 * @param {object}
		 */
		form: {},

		/**
		 * The input which holds the find replace value.
		 *
		 * {string}
		 */
		inputSelector: '#as3cf-remove-local-file',

		/**
		 * Is the option enabled or disabled on page load?
		 *
		 * {boolean}
		 */
		inputValueOnLoad: false,

		/**
		 * The element which contains our modal markup.
		 *
		 * {string}
		 */
		modalContainer: '.as3cf-remove-local-files-prompt',

		/**
		 * Sets the current input value on page load.
		 */
		setValueOnLoad: function() {
			this.inputValueOnLoad = this.optionEnabled();
		},

		/**
		 * Is the option enabled?
		 *
		 * @returns {boolean}
		 */
		optionEnabled: function() {
			return $( this.inputSelector ).is( ':checked' );
		},

		/**
		 * Maybe show prompt?
		 *
		 * @param {object} event
		 * @param {object} form
		 */
		maybeShowPrompt: function( event, form ) {
			if ( ! as3cfpro.Sidebar.isTabLocked( 'media' ) && this.optionEnabled() && false === this.inputValueOnLoad ) {
				event.preventDefault();

				this.form = form;
				this.showPrompt();
			}
		},

		/**
		 * Show remove file prompt.
		 */
		showPrompt: function() {
			as3cfModal.setDismissibleState( false );
			as3cfModal.open( this.modalContainer );
		},

		/**
		 * Handle prompt response.
		 *
		 * @param {object} event
		 */
		handlePromptResponse: function( event ) {
			var value = $( event.target ).data( 'remove-local-files' );

			this.setLoadingState();
			this.setHiddenInputValue( value );

			this.form.submit();
		},

		/**
		 * Disable buttons and show spinner.
		 */
		setLoadingState: function() {
			as3cfModal.setLoadingState( true );

			$( this.modalContainer + ' [data-remove-local-files]' )
				.prop( 'disabled', true )
				.siblings( '.spinner' )
				.css( 'visibility', 'visible' )
				.show();
		},

		/**
		 * Set input value to that of prompt response.
		 *
		 * @param {number} value
		 */
		setHiddenInputValue: function( value ) {
			$( this.form ).find( 'input[name=remove-local-files-prompt]' ).val( value );
		}
	};

	// Event Handlers
	$( document ).ready( function() {
		as3cfpro.Tools.RemoveLocalFiles.setValueOnLoad();

		// Listen for form submit
		$( '#tab-media .as3cf-main-settings form' ).on( 'submit', function( event ) {
			as3cfpro.Tools.RemoveLocalFiles.maybeShowPrompt( event, this );
		} );

		// Listen for prompt responses
		$( 'body' ).on( 'click', '.as3cf-remove-local-files-prompt [data-remove-local-files]', function( event ) {
			as3cfpro.Tools.RemoveLocalFiles.handlePromptResponse( event );
		} );
	} );

})( jQuery, as3cfModal );
