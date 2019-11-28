let headers
const xmlhttp = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject( 'Microsoft.XMLHTTP' )

window.onload = function () {
}

function removeFromCart ( url, product ) {
  headers = { 'Content-Type': 'application/json' }
  ajax( 'GET', headers, url + '?product=' + product, null, changeCart )
}

function addToCart ( url, isUserInCart ) {
  console.log( url )
  headers = { 'Content-Type': 'application/json' }
  // const items = +localStorage.getItem( 'amountInCart' ) || 0
  // alert( items );
  // [ ...document.querySelectorAll( '.items-in-cart' ) ].forEach( item => {
  //   item.innerText = items
  // } )
  ajax( 'GET', headers, url, null, !isUserInCart ? showPopUp : changeCart )
}

function removeProduct ( data, status ) {
  if ( status === 200 ) {
    document.getElementById( 'product_' + data ).remove()
  } else {
    alert( 'Smth went wrong' )
  }
}

function showPopUp ( data, status ) {
  if ( status === 200 ) {
    alert( 'Success' )
  } else {
    alert( 'Smth went wrong' )
  }
}

function changeCart ( data, status, url ) {
  if ( status === 200 ) {
    headers = { 'Content-Type': 'application/json' }
    ajax( 'GET', headers, '/cart?onlyItems=true', null, data => {
      document.querySelector( '.product-container' ).innerHTML = data
    } )
  } else {
    alert( 'Smth went wrong' )
  }
}

function ajax ( method, headers, url, params, callback ) {
  xmlhttp.onreadystatechange = function () {
    if ( xmlhttp.readyState === 4 ) {
      callback( xmlhttp.responseText, xmlhttp.status, url )
    }
  }
  xmlhttp.open( method, url, true )
  for ( let key in headers ) {
    xmlhttp.setRequestHeader( key, headers[ key ] )
  }
  xmlhttp.send( JSON.stringify( params ) )
}
