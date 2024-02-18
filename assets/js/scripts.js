;(function($){

    const fPlaceActions = async ( selector, action, date = null, id = null ) => {

        if( ! selector || ! action ){
            return false;
        }

        let data = {
            'action': action
        }

        date ? data.get_date    = date : null;
        id   ? data.id          = id : null;

        $.ajax({
            type: "POST",
            dataType: "html",
            url: fPlace.ajax_url,
            data: data,
            beforeSend : function ( xhr ) {
                selector.innerHTML = loader();
            },
            success: function( response ){
                if( response ){
                    selector.innerText = '';
                    selector.innerHTML = response;
                    fPlaceGetRoom();
                }
            },
            error : function(jqXHR, textStatus, errorThrown) {
                console.log(jqXHR);
            }
        });

        return false;
    }

    const loader = () =>{
        return`<div class="fplace-loader">
            <span id="flace_loading"></span>
        </div>`;
    }

    const fPlaceGetRoom = () =>{
        const roomInfoWrap  =  document.getElementsByClassName('fplace-room-book-info-inner');

        /**
         * Price calculation
         */
        if( roomInfoWrap.length > 0 ){
            for( const room of roomInfoWrap ){
                const extraPriceWrapp   = room.querySelector('.fplace_extra_price_check');
                const totalPriceWrap    = room.querySelector('.fplace-total-amount');
                const bookBtn           = room.querySelector('.fplace-book-btn');
    
                if( extraPriceWrapp ){
                    const price     = Number( totalPriceWrap.innerText );
                    const postId    = Number( bookBtn.getAttribute('data-id') );
                    extraPriceWrapp.addEventListener('change', function(event){
                        if( extraPriceWrapp.checked == true && totalPriceWrap ){
                            const exPrice = Number( extraPriceWrapp.value );
                            const totalPrice = price + exPrice;
                            totalPriceWrap.innerText = `${totalPrice}.00`;
                            sessionStorage.setItem("priceData", JSON.stringify({'extra': true, 'total_price': totalPrice, id: postId }));
                        }else{
                            totalPriceWrap.innerText = `${price}.00`;
                            sessionStorage.setItem("priceData", JSON.stringify({'extra': false, 'total_price': price, id: postId }));
                        }
                    });
                }
            }
        }
    }

    const fPlaceBookingMain = ()=> {
        const searchResult  = document.getElementById('fplace-search-result-container');
        const searchWrap    = document.getElementById('fplace-room-input');
        const searchButton  = document.getElementById('fplace-get-room-search');

        /**
         * Fplace search by date
         */
        if( searchWrap ){
            $( "#fplace-room-input" ).datepicker({
                showOn: "button",
                dateFormat: 'dd/mm/yy',
                showButtonPanel: true,
                buttonImage: "https://i.stack.imgur.com/cwClj.png?s=64&g=1"
            });
        }

        if( searchButton ){
            searchButton.addEventListener('click', function( event ){
                const getDate = searchWrap.value;
                if( getDate ){
                    fPlaceActions( searchResult, 'fplace_search_room', getDate );
                }
            });
        }
    }

    /**
     * Main function
     */
    fPlaceBookingMain();

})(jQuery);