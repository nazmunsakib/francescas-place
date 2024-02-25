;(function($){

    /**
     * Get Ajax Call
     * @param {*} selector 
     * @param {*} action 
     * @param {*} date 
     * @returns 
     */
    const fPlaceActions = async ( selector, action, date = null, userId = null ) => {

        if( ! selector || ! action ){
            return false;
        }

        let data = {
            'action': action,
            'nonce' : fPlace.nonce
        }

        date ? data.get_date    = date : null;
        userId ? data.user_id   = userId : null;
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
     * Get Ajax Call
     * @param {*} date 
     * @param {*} userId 
     * @param {*} name 
     *@param {*} email 
     * @returns 
     */
    const fPlaceWaitList = async ( action = 'submit_wait_list', selector = null, date = null, userId = null, name = null, email = null, post_id = null ) => {

        let data = {
            'action': action,
            'nonce' : fPlace.nonce
        }

        date ? data.get_date        = date : null;
        userId ? data.user_id       = userId : null;
        name ? data.customer_name   = name : null;
        email ? data.customer_email = email : null;
        post_id ? data.post_id      = post_id : null;

        $.ajax({
            type: "POST",
            dataType: "html",
            url: fPlace.ajax_url,
            data: data,
            beforeSend : function ( xhr ) {
                if( ('remove_wait_list' == action ) && selector ){
                    selector.innerText = 'Removing...';
                }
                if( ('submit_wait_list' == action ) && selector ){
                    let targetParent = selector.parentElement;
                    targetParent.style.opacity = '0.5';
                }
            },
            success: function( response ){
                let getData = JSON.parse( response );
                let targetParent = selector.parentElement;

                if( ( 'remove_wait_list' == action )  && selector ){
                    if( getData.success ){
                        let getRow = targetParent.parentElement;
                        if( getRow ){
                            getRow.remove();
                        }
                    }
                }

                if( ('submit_wait_list' == action ) && selector ){
                    targetParent.style.opacity = 1;
                    if( getData.success ){
                        window.location.href = `${fPlace.site_url}wait-list`
                    }else{
                        targetParent.innerHTML += "<p class='fplace-warning'>Something went wrong please try agin!</p>";
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
        const searchResult      = document.getElementById('fplace-search-result-container');
        const searchWrap        = document.getElementById('fplace-room-input');
        const searchButton      = document.getElementById('fplace-get-room-search');
        const cancelBooking     = document.getElementsByClassName('fplace-cancel-booking');
        const priceField        = document.querySelector('.fplace-proposed-booking-form-container #input_1_16');
        const proposedExPrice   = document.querySelector('.fplace-proposed-extra-price');
        const proposedPrice     = document.querySelector('.fplace-proposed-total-amount');
        const requestWaitList   = document.querySelector('.fplace-request-waitlist');
        const removeWaitList    = document.getElementsByClassName('fplace-wait-action');

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
                const getDate   = searchWrap.value;
                const userId    = this.getAttribute('data-userId');

                if( getDate ){
                    fPlaceActions( searchResult, 'fplace_search_room', getDate, userId );
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
         * Add Wait list
         */
        document.addEventListener('click', function( event ){
            const theWrapper = event.target;
            if( theWrapper.classList.contains('fplace-request-waitlist') ){
                event.preventDefault();

                const waitDate      = document.getElementById('waiting-date');
                const customerId    = document.getElementById('customer-id');
                const customerEmail = document.getElementById('customer-email');
                const customerName  = document.getElementById('customer-name');

                fPlaceWaitList( 'submit_wait_list', theWrapper, waitDate.value, customerId.value, customerName.value, customerEmail.value );
            }
        });

        /**
         * Remove from Wait list
         */
        if( removeWaitList.length > 0 ){
            for( let remove of removeWaitList ){
                remove.addEventListener('click', function( event ){
                    event.preventDefault();
                    if( remove ){
                        const post_id   = this.getAttribute('data-id');
                        console.log( post_id );
                        fPlaceWaitList( 'remove_wait_list', remove, null, null, null, null, post_id );
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
         * set price on Proposed Booking Details
         */
        if( proposedPrice ){
            const getPriceData = sessionStorage.getItem('priceData');
            const proposedExtraCheck     = document.querySelector('.fplace_proposed_extra_price_check');
            
            if( getPriceData ){
                const getPrice = JSON.parse(getPriceData);
                proposedPrice.innerHTML = getPrice.price;

                if( true === getPrice.extra ){

                    if( proposedExtraCheck ){
                        proposedExtraCheck.checked = true;
                    }

                    proposedPrice.innerHTML     = getPrice.price + getPrice.extra_price;
                    proposedExPrice.innerHTML   = getPrice.extra_price;
                }
            }
        }
    }

    /**
     * Main function
     */
    fPlaceBookingMain();

})(jQuery);