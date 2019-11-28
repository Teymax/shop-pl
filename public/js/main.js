( async () => {
  const nav = document.querySelector( 'nav' )
  const navPos = nav.offsetTop
  // const mobMenu = document.querySelector( '.menu' )
  document.addEventListener( 'scroll', function ( e ) {
    if ( scrollY > navPos ) {
      nav.classList.add( 'pinned' )
    } else if ( scrollY < navPos ) {
      nav.classList.remove( 'pinned' )
    } else {
      nav.classList.remove( 'pinned' )
    }
  } )

  $( document ).ready( function () {
    $( '#nav-icon3' ).click( function () {
      $( this ).toggleClass( 'open' )
      $( '.menu' ).toggleClass( 'menu-show' )
      if ( $( '#shadowOverlay' )[ 0 ].style.display === 'none' ) {
        $( '#shadowOverlay' ).fadeIn()
      } else {
        $( '#shadowOverlay' ).fadeOut()
      }
    } )

    $( '#shadowOverlay' ).click( function () {
      $( '#shadowOverlay' ).fadeOut()
      $( '.menu' ).toggleClass( 'menu-show' )
      $( '#nav-icon3' ).toggleClass( 'open' )
    } )
  } )
} )()
