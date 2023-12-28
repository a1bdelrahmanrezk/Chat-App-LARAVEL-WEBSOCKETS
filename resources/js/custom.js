$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$(document).ready(function () { // open chat click
    $('.user-list').click(function (handler) {
        $('#chat-container').html('');
        var getUserId = $(this).attr('data-id');
        receiver_id = getUserId;
        $('.start-head').hide();
        $('.chat-section').show();
        loadOldChat(); // load old chats
    });
    // save chat
    $('#chat-form').submit(function (handler) {
        handler.preventDefault();
        var message = $('#message').val();
        $.ajax({
            url: "/save-chat",
            type: "POST",
            data: {
                sender_id: sender_id,
                receiver_id: receiver_id,
                message: message
            },
            success: function (response) {
                if (response.success) {
                    $('#message').val();
                    let chat = response.data.message;
                    let html = `
                    <div class="current-user-chat text-end" id='`+ response.data.id + `-chat'>
                        <p id="chat-message-p-`+ response.data.id + `">` + chat + `</p>
                        <span id="delete-ico" class='cursor-pointer' data-bs-toggle="modal" data-bs-target="#deleteChatModel" data-id='`+ response.data.id + `'>
                            <svg fill="red" xmlns="http://www.w3.org/2000/svg" height="16" width="14" viewBox="0 0 448 512"><path d="M135.2 17.7L128 32H32C14.3 32 0 46.3 0 64S14.3 96 32 96H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H320l-7.2-14.3C307.4 6.8 296.3 0 284.2 0H163.8c-12.1 0-23.2 6.8-28.6 17.7zM416 128H32L53.2 467c1.6 25.3 22.6 45 47.9 45H346.9c25.3 0 46.3-19.7 47.9-45L416 128z"/></svg>
                        </span>
                        <span id="update-ico" class='cursor-pointer' data-bs-toggle="modal" data-bs-target="#updateChatModel" data-id='`+ response.data.id + `' data-message='` + response.data.message + `'>
                        <svg fill="green" xmlns="http://www.w3.org/2000/svg" height="20" width="20" viewBox="0 0 512 512"><path d="M441 58.9L453.1 71c9.4 9.4 9.4 24.6 0 33.9L424 134.1 377.9 88 407 58.9c9.4-9.4 24.6-9.4 33.9 0zM209.8 256.2L344 121.9 390.1 168 255.8 302.2c-2.9 2.9-6.5 5-10.4 6.1l-58.5 16.7 16.7-58.5c1.1-3.9 3.2-7.5 6.1-10.4zM373.1 25L175.8 222.2c-8.7 8.7-15 19.4-18.3 31.1l-28.6 100c-2.4 8.4-.1 17.4 6.1 23.6s15.2 8.5 23.6 6.1l100-28.6c11.8-3.4 22.5-9.7 31.1-18.3L487 138.9c28.1-28.1 28.1-73.7 0-101.8L474.9 25C446.8-3.1 401.2-3.1 373.1 25zM88 64C39.4 64 0 103.4 0 152V424c0 48.6 39.4 88 88 88H360c48.6 0 88-39.4 88-88V312c0-13.3-10.7-24-24-24s-24 10.7-24 24V424c0 22.1-17.9 40-40 40H88c-22.1 0-40-17.9-40-40V152c0-22.1 17.9-40 40-40H200c13.3 0 24-10.7 24-24s-10.7-24-24-24H88z"/></svg>
                        </span>
                    </div>`;
                    $('#chat-container').append(html);
                    scrollChat();
                }
                else {
                    window.alert(response.errorMessage);
                }
            }
        });
        $('#message').val('');
    });
    $(document).on('click', '#delete-ico', function () {
        var id = $(this).attr('data-id');
        $('#delete-chat-id').val(id);
        $('#delete-message-q').text(' ' + $('#chat-message-p-' + id).text() + ' ');
    });
    $('#delete-chat-form').submit(function (handler) {
        handler.preventDefault();
        var id = $('#delete-chat-id').val();
        $.ajax({
            url: "delete-chat",
            type: "POST",
            data: {
                id: id
            },
            success: function (response) {
                window.alert(response.message);
                if (response.success) {
                    $('#' + id + '-chat').remove();
                    $('#deleteChatModel').modal('hide');
                }
            }
        });
    });
    // update chat message
    $(document).on('click', '#update-ico', function () {
        $('#update-chat-id').val($(this).attr('data-id'));
        $('#update-message').val($(this).attr('data-message'));
    });
    $('#update-chat-form').submit(function (handler) {
        handler.preventDefault();
        var id = $('#update-chat-id').val();
        var message = $('#update-message').val();
        $.ajax({
            url: "/update-chat",
            type: "POST",
            data: {
                id: id,
                message: message,
            },
            success: function (response) {
                if (response.success) {
                    $('#updateChatModel').modal('hide');
                    // $('#'+id+'-chat').find('p').text(message);
                    $('#chat-message-p-' + id).text(message);
                    $('#update-ico').attr('data-message', message);
                } else {
                    alert(response.message);
                }
            }
        });
    });
});
function loadOldChat() {
    $.ajax({
        url: "/load-chats",
        type: "POST",
        data: {
            sender_id: sender_id,
            receiver_id: receiver_id
        },
        success: function (response) {
            if (response.success) {
                let chats = response.data;
                let html = '';
                chats.forEach(chat => {
                    let addClass = '';
                    let deleteIconBtn = '';
                    let udpateIconBtn = '';
                    if (chat.sender_id == sender_id) {
                        addClass = 'current-user-chat text-end';
                        deleteIconBtn = `
                        <span id="delete-ico" class='cursor-pointer' data-bs-toggle="modal" data-bs-target="#deleteChatModel" data-id='`+ chat.id + `'>
                            <svg fill="red" xmlns="http://www.w3.org/2000/svg" height="16" width="14" viewBox="0 0 448 512"><path d="M135.2 17.7L128 32H32C14.3 32 0 46.3 0 64S14.3 96 32 96H416c17.7 0 32-14.3 32-32s-14.3-32-32-32H320l-7.2-14.3C307.4 6.8 296.3 0 284.2 0H163.8c-12.1 0-23.2 6.8-28.6 17.7zM416 128H32L53.2 467c1.6 25.3 22.6 45 47.9 45H346.9c25.3 0 46.3-19.7 47.9-45L416 128z"/></svg>
                        </span>
                        `;
                        udpateIconBtn = `
                        <span id="update-ico" class='cursor-pointer' data-bs-toggle="modal" data-bs-target="#updateChatModel" data-id='`+ chat.id + `' data-message='` + chat.message + `'>
                        <svg fill="green" xmlns="http://www.w3.org/2000/svg" height="20" width="20" viewBox="0 0 512 512"><path d="M441 58.9L453.1 71c9.4 9.4 9.4 24.6 0 33.9L424 134.1 377.9 88 407 58.9c9.4-9.4 24.6-9.4 33.9 0zM209.8 256.2L344 121.9 390.1 168 255.8 302.2c-2.9 2.9-6.5 5-10.4 6.1l-58.5 16.7 16.7-58.5c1.1-3.9 3.2-7.5 6.1-10.4zM373.1 25L175.8 222.2c-8.7 8.7-15 19.4-18.3 31.1l-28.6 100c-2.4 8.4-.1 17.4 6.1 23.6s15.2 8.5 23.6 6.1l100-28.6c11.8-3.4 22.5-9.7 31.1-18.3L487 138.9c28.1-28.1 28.1-73.7 0-101.8L474.9 25C446.8-3.1 401.2-3.1 373.1 25zM88 64C39.4 64 0 103.4 0 152V424c0 48.6 39.4 88 88 88H360c48.6 0 88-39.4 88-88V312c0-13.3-10.7-24-24-24s-24 10.7-24 24V424c0 22.1-17.9 40-40 40H88c-22.1 0-40-17.9-40-40V152c0-22.1 17.9-40 40-40H200c13.3 0 24-10.7 24-24s-10.7-24-24-24H88z"/></svg>
                        </span>
                        `;
                    } else {
                        addClass = 'destination-user-chat text-start';
                    }
                    html += `
                    <div class="`+ addClass + `" id='` + chat.id + `-chat'>
                    <p id="chat-message-p-`+ chat.id + `">` + chat.message + `</p>
                    `+
                        deleteIconBtn
                        + `
                    `+
                        udpateIconBtn
                        + `
                    </div>
                    `;
                });
                $('#chat-container').append(html);
                scrollChat();
            } else {
                window.alert(response.errorMessage);
            }
        }
    });
}
Echo.join('status-update')
    .here((users) => {
        users.forEach(user => {
            if (sender_id != user.id) {
                $('#' + user.id + '-status').removeClass('offline-status');
                $('#' + user.id + '-status').addClass('online-status');
                $('#' + user.id + '-status-icon-offline').removeClass('offline-status');
                $('#' + user.id + '-status-icon-offline').addClass('online-status');
                $('#' + user.id + '-status').text('Online');
            }
        });

    }).joining((user) => {
        $('#' + user.id + '-status').removeClass('offline-status');
        $('#' + user.id + '-status').addClass('online-status');
        $('#' + user.id + '-status-icon-offline').removeClass('offline-status');
        $('#' + user.id + '-status-icon-offline').addClass('online-status');
        $('#' + user.id + '-status').text('Online');
    }).leaving((user) => {
        $('#' + user.id + '-status').removeClass('online-status');
        $('#' + user.id + '-status').addClass('offline-status');
        $('#' + user.id + '-status-icon-offline').removeClass('online-status');
        $('#' + user.id + '-status-icon-offline').addClass('offline-status');
        $('#' + user.id + '-status').text('Offline');
    }).listen('UserStatusEvent', (e) => {
        console.log('aaaaaaaaaaaaa' + e);
    });

Echo.private('broadcast-message')
    .listen('.getChatMessage', (data) => {
        if (sender_id == data.chat.receiver_id && receiver_id == data.chat.sender_id) {
            let html = `
        <div class="destination-user-chat text-start" id='`+ data.chat.id + `-chat'>
        <p id="chat-message-p-`+ data.chat.id + `">` + data.chat.message + `</p>
        <span id="delete-ico" class='cursor-pointer' data-bs-toggle="modal" data-bs-target="#deleteChatModel" data-id='`+ data.chat.id + `'>
        </span>
        <span id="update-ico" class='cursor-pointer' data-bs-toggle="modal" data-bs-target="#updateChatModel" data-id='`+ data.chat.id + `' data-message='` + data.chat.message + `'>
        </span>
        </div>
        `;
        
            $('#chat-container').append(html);
            scrollChat();
        }
    });

function scrollChat() { // scroll to down
    $('#chat-container').animate({
        scrollTop: $('#chat-container').offset().top + $('#chat-container')[0].scrollHeight
    }, 0);
}
// delete chat message listen
Echo.private('message-deleted')
    .listen('deleteMessageEvent', (data) => {
        $('#' + data.id + '-chat').remove();
    });


// update chat message listen
Echo.private('message-updated')
    .listen('updateMessageEvent', (data) => {
        $('#chat-message-p-' + data.data.id).text(data.data.message);
        $('#' + data.data.id + '-chat').find('#update-ico').attr('data-message', data.data.message);
        // scrollChat();
    });