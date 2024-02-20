;(function($){

    /**
     * Get Ajax Call
     * @param {*} selector 
     * @param {*} action 
     * @param {*} date 
     * @returns 
     */
    const fPlaceActions = async ( selector, action, date = null ) => {

        if( ! selector || ! action ){
            return false;
        }

        let data = {
            'action': action
        }

        date ? data.get_date    = date : null;
        selector.getAttribute('data-bookedId')  ? data.booked_id    = selector.getAttribute('data-bookedId') : null;
        selector.getAttribute('data-roomId')    ? data.room_id      = selector.getAttribute('data-roomId') : null;

        $.ajax({
            type: "POST",
            dataType: "html",
            url: fPlace.ajax_url,
            data: data,
            beforeSend : function ( xhr ) {
                if( 'cancel_booking' === action ){
                    selector.innerHTML = '<span>...</span>';
                }else{
                    selector.innerHTML = loader();
                }
            },
            success: function( response ){
                if( response ){
                    if( 'cancel_booking' === action ){
                        selector.parentElement.innerHTML = response;
                    }else{
                        selector.innerHTML = response;
                        fPlaceGetPrice();
                    }
                }
            },
            error : function(jqXHR, textStatus, errorThrown) {
                console.log(jqXHR);
            }
        });

        return false;
    }

    /**
     * Ajax Calling loader
     * @returns 
     */
    const loader = () =>{
        return`<div class="fplace-loader">
            <span id="flace_loading"></span>
        </div>`;
    }

    /**
     * calculation booking price
     */
    const fPlaceGetPrice = () =>{
        const roomInfoWrap  =  document.getElementsByClassName('fplace-room-book-info-inner');

        /**
         * Price calculation
         */
        if( roomInfoWrap.length > 0 ){
            for( const room of roomInfoWrap ){
                const extraPriceWrapp   = room.querySelector('.fplace_extra_price_check');
                const totalPriceWrap    = room.querySelector('.fplace-total-amount');
                const bookBtn           = room.querySelector('.fplace-book-btn');
    
                if( extraPriceWrapp && bookBtn ){
                    const price     = Number( bookBtn.getAttribute('data-price') );

                    extraPriceWrapp.addEventListener('change', function(event){
                        if( extraPriceWrapp.checked == true && totalPriceWrap ){
                            const exPrice = Number( extraPriceWrapp.value );
                            const totalPrice = price + exPrice;
                            totalPriceWrap.innerText = `${totalPrice}.00`;
                        }else{
                            totalPriceWrap.innerText = `${price}.00`;
                        }
                    });
                }

                /**
                 * Set price on session storage
                 */
                if( bookBtn && extraPriceWrapp ){
                    const postId    = Number( bookBtn.getAttribute('data-id') );
                    bookBtn.addEventListener('click', function(e){
                        const price     = Number( bookBtn.getAttribute('data-price') );
                        if( extraPriceWrapp.checked == true  ){
                            const extraPrice = Number( extraPriceWrapp.value )
                            sessionStorage.setItem("priceData", JSON.stringify({'extra': true, 'price': price, 'extra_price': extraPrice, id: postId }));
                        }else{
                            sessionStorage.setItem("priceData", JSON.stringify({'extra': false, 'price': price, id: postId }));
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
        const cancelBooking = document.getElementsByClassName('fplace-cancel-booking');
        const priceField    = document.querySelector('.fplace-proposed-booking-form-container #input_1_16');

        /**
         * Search datepicker
         */
        if( searchWrap ){
            $( "#fplace-room-input" ).datepicker({
                showOn: "button",
                dateFormat: 'dd/mm/yy',
                showButtonPanel: true,
                buttonText: ' '
            });
        }

        /**
         * Check Availability
         */
        if( searchButton ){
            searchButton.addEventListener('click', function( event ){
                const getDate = searchWrap.value;
                if( getDate ){
                    fPlaceActions( searchResult, 'fplace_search_room', getDate );
                }
            });
        }

        /**
         * Cancel Booking
         */
        if( cancelBooking.length > 0 ){
            for( let cancel of cancelBooking ){
                cancel.addEventListener('click', function( event ){
                    event.preventDefault();
                    if( cancel ){
                        const getDate   = this.getAttribute('data-bookingdate');
                        fPlaceActions( cancel, 'cancel_booking', getDate);
                    }
                });
            }
        }

        /**
         * set total price value on price field
         */
        if( priceField ){
            const getPriceData = sessionStorage.getItem('priceData');
            if( getPriceData ){
                const getPrice = JSON.parse(getPriceData);
                priceField.value = getPrice.price;
                if( true === getPrice.extra ){
                    priceField.value = getPrice.price + getPrice.extra_price;
                    console.log( getPrice.price + getPrice.extra_price );
                }
            }
        }

        /**
         * Clear session storage data 
         * after booking
         */
        setTimeout( function() {
            sessionStorage.removeItem('priceData');
        }, 5 * 60 * 1000);

    }

    /**
     * Main function
     */
    fPlaceBookingMain();

})(jQuery);