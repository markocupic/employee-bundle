//Provides methods for filtering the kursliste

var SacEventFilter =
    {
        /**
         * globalEventId
         * This is used in self.queueRequest
         */
        globalEventId: 0,

        /**
         * time to wait before launching the xhr request, when making changes to the filter form
         */
        delay: 1000,


        /**
         * queueRequest
         */
        queueRequest: function () {
            SacEventFilter.showLoadingIcon();
            SacEventFilter.resetEventList();
            SacEventFilter.globalEventId++;
            var eventId = SacEventFilter.globalEventId;
            window.setTimeout(function () {
                if (eventId == SacEventFilter.globalEventId) {
                    SacEventFilter.fireXHR();
                }
            }, SacEventFilter.delay);
        },

        /**
         * Reset/Remove List
         */
        resetEventList: function () {
            $('.alert-no-results-found').remove();
            $('.kursmodul-sac-liste').each(function () {
                $(this).hide();
                $(this).removeClass('visible');
            });
        },


        /**
         * Show the loading icon
         */
        showLoadingIcon: function () {
            // Add loading icon
            $('.loading-icon-lg').remove();
            $('.mod_eventlist').append('<div class="loading-icon-lg"><div><span class="fa fa-spinner fa-spin"></span></div></div>');
        },

        /**
         * Hide the loading icon
         */
        hideLoadingIcon: function () {
            // Add loading icon
            $('.loading-icon-lg').remove();
        },

        /**
         * List events starting from a certain date
         * @param dateStart
         */
        listEventsStartingFromDate: function (dateStart) {
            var regex = /^(.*)-(.*)-(.*)$/g;
            var match = regex.exec(dateStart);
            if (match) {
                // JavaScript counts months from 0 to 11. January is 0. December is 11.
                var date = new Date(match[3], match[2] - 1, match[1]);
                var tstamp = Math.round(date.getTime() / 1000);
                if (!isNaN(tstamp)) {
                    $('#ctrl_dateStartHidden').val(tstamp);
                    $('#ctrl_dateStartHidden').attr('value', tstamp);
                    SacEventFilter.queueRequest();
                    return;
                }
            }
            $('#ctrl_dateStartHidden').attr('value', '0');
            $('#ctrl_dateStartHidden').val('0');
            SacEventFilter.queueRequest();
        },


        /**
         * filterRequest
         */
        fireXHR: function () {
            var itemsFound = 0;

            // Event-items
            var arrIds = [];
            $('.kursmodul-sac-liste').each(function () {
                arrIds.push($(this).attr('data-id'));
            });

            // Kursart
            var idKursart = $('#ctrl_courseTypeLevel1').val();
            // Save Input to sessionStorage
            try {
                sessionStorage.setItem('ctrl_courseTypeLevel1', idKursart);
            }
            catch (e) {
                console.log('Session Storage is disabled or not supported on this browser.')
            }

            // Sektionen
            var arrOGS = [];
            $('.ctrl_sektion:checked').each(function () {
                arrOGS.push(this.value);
            });

            try {
                // Save Input to sessionStorage
                sessionStorage.setItem('ctrl_sektion', JSON.stringify(arrOGS));
            }
            catch (e) {
                console.log('Session Storage is disabled or not supported on this browser.')
            }

            // StartDate
            var intStartDate = Math.round($('#ctrl_dateStartHidden').val()) > 0 ? $('#ctrl_dateStartHidden').val() : 0;
            intStartDate = Math.round(intStartDate);

            // Textsuche
            var strSuchbegriff = $('#ctrl_suche').val();
            // Save Input to sessionStorage
            try {
                sessionStorage.setItem('ctrl_suche', strSuchbegriff);
            }
            catch (e) {
                console.log('Session Storage is disabled or not supported on this browser.')
            }

            var url = window.location.href;
            var request = $.ajax({
                method: 'post',
                url: url,
                data: {
                    action: 'filterKursliste',
                    REQUEST_TOKEN: request_token,
                    ids: JSON.stringify(arrIds),
                    kursart: idKursart,
                    ogs: JSON.stringify(arrOGS),
                    suchbegriff: strSuchbegriff,
                    startDate: intStartDate
                },
                dataType: 'json'
            });
            request.done(function (json) {
                if (json) {
                    SacEventFilter.hideLoadingIcon();

                    $.each(json.filter, function (key, id) {
                        $('.kursmodul-sac-liste[data-id="' + id + '"]').each(function () {
                            //intFound++;
                            $(this).show();
                            $(this).addClass('visible');
                            itemsFound++;
                        });
                    });
                    if (itemsFound == 0 && $('.alert-no-results-found').length == 0) {

                        $('.mod_eventlist').append('<div class="alert alert-danger alert-no-results-found text-lg" role="alert"><h4><i class="fa fa-meh-o" aria-hidden="true"></i> Leider wurden zu deiner Suchanfrage keine Events gefunden.</h4></div>');
                    }
                }
            });
            request.fail(function (jqXHR, textStatus, errorThrown) {
                SacEventFilter.hideLoadingIcon();
                console.log(jqXHR);
                alert('Fehler: Die Anfrage konnte nicht bearbeitet werden! Überprüfe Sie die Internetverbindung.');
            });
        },
        /**
         * get url param
         * @param strParam
         * @returns {*|number}
         */
        getUrlParam: function (strParam) {
            var results = new RegExp('[\?&]' + strParam + '=([^&#]*)').exec(window.location.href);
            if (results === null) return 0;
            return results[1] || 0;
        }

    }


// Check if the request token is set
$().ready(function () {
    if (!request_token) {
        alert('Please set the request-token in the template');
    }

    // filter List if there are some values in the browser's sessionStorage
    try {
        if (typeof window.sessionStorage !== 'undefined') {
            var blnFilterList = false;
            if (sessionStorage.getItem('ctrl_suche') !== null) {
                $('#ctrl_suche').val(sessionStorage.getItem('ctrl_suche'));
                blnFilterList = true;
            }

            if (sessionStorage.getItem('ctrl_sektion') !== null) {
                var arrSektionen = JSON.parse(sessionStorage.getItem('ctrl_sektion'));
                if (arrSektionen.length) {
                    $('.ctrl_sektion').each(function () {
                        if (jQuery.inArray($(this).attr('value'), arrSektionen) > -1) {
                            $(this).prop('checked', true);
                        } else {
                            $(this).prop('checked', false);
                        }
                    });
                    blnFilterList = true;
                }
            }

            if (sessionStorage.getItem('ctrl_courseTypeLevel1') !== null) {
                $('#ctrl_courseTypeLevel1').val(sessionStorage.getItem('ctrl_courseTypeLevel1'));
                blnFilterList = true;
            }


            // Filter list, if there are some filter stored in the sessionStorage
            if (blnFilterList) {
                SacEventFilter.resetEventList();
                SacEventFilter.queueRequest();
            }
        }
    }
    catch (e) {
        console.log('Session Storage is disabled or not supported for this browser.')
    }


});


// Init iCheck
$().ready(function () {
    // http://icheck.fronteed.com/
    $('#organizers input').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-grey',
        increaseArea: '20%' // optional
    });
});


/** Trigger Filter **/
$().ready(function () {

    // Redirect to selected year
    $('#ctrl_eventYear').on('change', function () {
        var arrUrl = window.location.href.split("?");
        window.location.href = arrUrl[0] + '?year=' + $(this).prop('value');
    });

    $('#ctrl_courseTypeLevel1, .ctrl_sektion').on('change', function () {
        SacEventFilter.queueRequest();
    });

    $('#ctrl_suche').on('keyup', function () {
        SacEventFilter.queueRequest();
    });

    $('#organizers input').on('ifClicked', function (event) {
        SacEventFilter.queueRequest();
    });

    // List events starting from a certain date
    var dateStart = $('#ctrl_dateStart').val();
    SacEventFilter.listEventsStartingFromDate(dateStart);


    /** Set Datepicker **/
    var opt = {
        format: "dd-mm-yyyy",
        autoclose: true,
        maxViewMode: 0,
        language: "de"
    };
    // Set datepickers start and end date
    if (SacEventFilter.getUrlParam('year') > 0) {
        opt['startDate'] = "01-01-" + SacEventFilter.getUrlParam('year');
        opt['endDate'] = "31-12-" + SacEventFilter.getUrlParam('year');
    } else {
        var now = new Date();
        opt['startDate'] = "01-01-" + now.getFullYear();
        opt['endDate'] = "31-12-" + now.getFullYear();
    }

    $('.filter-board .input-group.date').datepicker(opt).on('changeDate', function (e) {
        var dateStart = $('#ctrl_dateStart').val();
        SacEventFilter.listEventsStartingFromDate(dateStart);
    });
});


// Entferne die Suchoptionen im Select-Menu, wenn ohnehin keine Events dazu existieren
$().ready(function () {
    var kursarten = [];
    $('.kursmodul-sac-liste').each(function () {
        if ($(this).attr('data-courseTypeLevel1') != '') {
            var arten = $(this).attr('data-courseTypeLevel1').split(',');
            jQuery.each(arten, function (i, val) {
                kursarten.push(val);
            });
        }
    });
    kursarten = jQuery.unique(kursarten);
    $('#ctrl_courseTypeLevel1 option').each(function () {
        if ($(this).attr('value') > 0) {
            var id = $(this).attr('value');
            if (jQuery.inArray(id, kursarten) < 0) {
                $(this).remove();
            }
        }
    });
});
