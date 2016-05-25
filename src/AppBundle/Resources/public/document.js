/**
 * Created by alin.ionescu on 10.05.2016.
 */
$(document).ready(function ()
{
    $('#add-document').click(function (event) {
        event.preventDefault();

        var $dialog = $('<div id="addDocument"/>'),
            $iframe = $('<iframe id="addDocumentIframe"/>');
        var url = "";

        $iframe.attr('src', url)
            .css({
                'width': 0.5 * $(window).width(),
                'height':'100%',
                'overflow-x': 'hidden',
                'border': 0
            })
            .appendTo($dialog);
        $dialog
            .css({
                'z-index': 99999999,
                'background-color': '#fff',
                'text-align': 'center',
                'overflow': 'hidden'
            })
            .appendTo('body')
            .dialog({
                'title': "Adauga Document",
                'width': 0.5 * $(window).width(),
                'height': 0.8 * $(window).height(),
                'position': 'center',
                'resizable': false,
                'closeOnEscape': false,
                'modal': true,
                'buttons': [
                    {
                        text: 'Cancel',
                        click: function ()
                        {
                            window.hideLoadingModal();
                            $dialog.dialog('close');
                        }
                    },
                    {
                        text:  "Salveaza",
                        click: function () {
                            window.showLoadingModal();
                            var $details = $('#addDocumentIframe');
                            var $form = $("#order-cancel", $details.contents()).serialize();
                            if ($form == "") {
                                window.hideLoadingModal();
                                showPopup('order_details.actions.operations.cancellation.select_reason', {
                                    title: "altceva",
                                    buttons: {
                                        "Ok": function () {
                                            window.hideLoadingModal();
                                            $(this).dialog("close");
                                        }
                                    }
                                });
                            } else {
                                $.ajax({
                                    url: url,
                                    type: 'POST',
                                    data: $form,
                                    success: function (data) {
                                        window.hideLoadingModal();
                                        if (data.error) {
                                            showPopup(data.message, {
                                                title: Translator.get('order_details.actions.operations.cancellation.title'),
                                                buttons: {
                                                    "Ok": function () {
                                                        window.hideLoadingModal();
                                                        $(this).dialog("close");
                                                    }
                                                }
                                            });
                                        }
                                        else {
                                            showPopup(data.message, {
                                                title: 'Succes',
                                                buttons: {
                                                    "Ok": function () {
                                                        var route = Routing.generate('order-details', {orderId: orderId, '_locale': locale});
                                                        window.location.href =  route;
                                                        $(this).dialog("close");
                                                        window.showLoadingModal();
                                                    }
                                                }
                                            });
                                        }
                                    }
                                })
                            }
                        }
                    }
                ],
                'open': function (e, ui)
                {
                    $('body').css('overflow', 'hidden');
                    $dialog.prev().hide();
                },
                'close': function (e, ui)
                {
                    $('body').css('overflow', 'auto');
                    $dialog.remove();
                }
            });
    })
});