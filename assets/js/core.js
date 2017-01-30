$(function() {

    var page_url = window.location.href;

    var dc_block = $('#dcomment-block');


    var form_add = '<form action="#" type="POST" class="dcomment-add">' +
        '<textarea name="comment[message]" contenteditable="true"></textarea>' +
        '<input type="submit" value="send">' +
        '</form>';

    var form_template = '<form action="#" type="POST">' +
        '<textarea name="comment[message]" contenteditable="true"></textarea>' +
        '<input type="submit" value="send">' +
        '</form>';
    var user_email = '';
    var comments_data = new Array();

    var comment_auth_options = '<div class="dcomment-options"><a href="#" class="dcomment-edit-button">edit</a> <a href="#"  class="dcomment-replay-button">replay</a> <a href="#"  class="dcomment-delete-button">delete</a> </div>';
    var comment_options = '<div class="dcomment-options"><a href="#"  class="dcomment-replay-button">replay</a></div>';
    var comment_rating = '<div class="dcomment-rating-buttons"><a href="#" class="dcomment-add-rating">+</a> | <a href="#" class="dcomment-remove-rating">-</a></div>';


    function comments_load() {
        dc_block.html('').addClass('loader');

        $.ajax({
            type: 'POST',
            url: "/comment/page",
            data: {
                url: page_url
            },
            success: function(json){
                var data = $.parseJSON(json);
                user_email = data['user_email'];
                var comments = commentsTree(data['comments'], 0);

                comments = '<ul class="comments-list">' + comments + '</ul>';

                $('#dcomment-block').removeClass('loader').append(form_add);
                $('#dcomment-block').append(comments);
            }
        });
    }


    function comment_template(comment_data, comment_only) {

        var base_data = {
            ul_class : '',
            li_class : '',
            id : '',
            email : '',
            message : '',
            rating : '',
            rating_buttons : '',
            options : '',
            children : ''
        };

        var data = $.extend(base_data, comment_data);

        if (!data['children']) {
            data['children'] = '<ul class="children"></ul>';
        }

        var result =
            '<li class="comment ' + data['li_class'] + '">' +
            '<div class="comment-body" data-id="' + data['id'] + '">' +
            '<div class="comment-info">' + data['email'] + ' | ' + data['date'] + '</div>' +
            '<div class="comment-text">' + data['message'] + '</div>' +
            '<div class="comment-menu">' +
                '<div class="dcomment-rating">' + data['rating'] + '</div>' +
                data['rating_buttons'] +
                data['options'] +
                '<div class="clear"></div>' +
            '</div>' +
            '</div>' +
                data['children'] +
            '</li>';

        if (!comment_only) {
            result = '<ul class="' + data['ul_class'] + '">' + result + '</ul>';
        }

        return result;
    }
    comments_load();

    function commentsTree(data, parent_id){
        var ul_class = '';
        var li_class = '';
        var id = '';
        var email = '';
        var message = '';
        var rating = '';
        var options = '';
        var children = '';

        var comment;
        var comments_only = true;

        if (parent_id != 0) {
            ul_class = 'children';
            comments_only = false;
        }
        var tree = ''
        if(data[parent_id]){

            for (var item in data[parent_id]) {

                comment = data[parent_id][item];

                // GLOBAL
                comments_data[comment['id']] = {
                    user : comment['email'],
                    message: comment['message']
                };
                //


                options = comment_options;
                li_class = '';
                rating = comment_rating;
                if (comment['email'] == user_email) {
                    options = comment_auth_options;
                    rating = '';
                }
                if (comment['active'] == 0) {
                    options = '';
                    rating = '';
                    li_class = 'comment-deleted';
                }
                comment['options'] = options;
                comment['rating_buttons'] = rating;
                comment['ul_class'] = ul_class;
                comment['li_class'] = li_class;
                comment['children'] = commentsTree(data, comment['id']);
                tree += comment_template(comment, comments_only);
            }
        }
        return tree;
    }


    $(document).on('click', '.dcomment-replay-button', function(e) {
        e.preventDefault();
        if ($(this).hasClass('active')) {
            $(this).removeClass('active');
            $(this).closest('.comment').children('form').remove();
        } else {
            $(this).addClass('active');
            $(this).closest('.comment').children('.comment-body').after(form_template);
            $(this).closest('.comment').children('form').addClass('dcomment-replay');
        }
    });


    $(document).on('click', '.dcomment-edit-button', function(e) {
        e.preventDefault();
        var update_comment = $(this).closest('.comment-body');

        var test_block = update_comment.find('.comment-text');
        var id = update_comment.attr('data-id');
        var old_text = comments_data[id]['message'];
        test_block.hide();
        test_block.after(form_template);
        update_comment.find('form').addClass('dcomment-edit');
        update_comment.find('textarea').val(old_text);
    });


    $(document).on('click', '.dcomment-delete-button', function(e) {
        e.preventDefault();
        var comment = $(this).closest('.comment-body');

        var id = comment.attr('data-id');
        $.ajax({
            type: 'POST',
            url: "/comment/delete",
            data: {comment_id : id},
            success: function(json){
                var data = $.parseJSON(json);
                if (data['status'] == 1) {
                    comment.closest('li').addClass('comment-deleted');
                    comment.find('.comment-text').html('This comment was deleted.');
                    comment.find('.dcomment-options').remove();
                }
            }
        });
    });



    $(document).on('submit', '.dcomment-add', function(e) {
        e.preventDefault();
        var add_form = $(this).closest('form');
        var message = add_form.find('textarea').val();
        $.ajax({
            type: 'POST',
            url: "/comment/add",
            data: {
                url: page_url,
                message: message,
                parent_id: 0
            },
            success: function(json){
                var data = $.parseJSON(json);

                if (data['status'] == 1 && data['comment_id'] && data['comment_time']) {
                    comments_data[data['comment_id']] = {message : message, user: user_email};

                    var comment = {
                        id : data['comment_id'],
                        email : user_email,
                        date : data['comment_time'],
                        rating : 0,
                        message : message,
                        options : comment_auth_options
                    };

                    var add_comment = comment_template(comment, true);
                    $('.comments-list').append(add_comment);
                    add_form.find('textarea').val('');
                } else {
                    alert(data['error']);
                }
            }
        });
    });


    $(document).on('submit', '.dcomment-edit', function(e) {
        e.preventDefault();
        var comment_block = $(this).closest('.comment-body');
        var id = comment_block.attr('data-id');
        var edit_form = $(this).closest('form');
        var message = edit_form.find('textarea').val();


        $.ajax({
            type: 'POST',
            url: "/comment/edit",
            data: {
                url: page_url,
                message: message,
                comment_id: id
            },
            success: function(json){
                var data = $.parseJSON(json);

                if (data['status'] == 1) {
                    edit_form.remove();
                    comment_block.find('.comment-text').html(message).show();
                    comments_data[id]['message'] = message;
                }
            }
        });
    });


    $(document).on('submit', '.dcomment-replay', function(e) {
        e.preventDefault();
        var comment_element = $(this).closest('.comment');
        var form = $(this).closest('form');
        var message = form.find('textarea').val();
        var parent_id = comment_element.children('.comment-body').attr('data-id');
        $.ajax({
            type: 'POST',
            url: "/comment/add",
            data: {
                url: page_url,
                message: message,
                parent_id: parent_id
            },
            success: function(json){
                var data = $.parseJSON(json);

                if (data['status'] == 1 && data['comment_id'] && data['comment_time']) {
                    comments_data[data['comment_id']] = {message : message, user: user_email};

                    var comment = {
                        id : data['comment_id'],
                        email : user_email,
                        date : data['comment_time'],
                        message : message,
                        options : comment_auth_options
                    };

                    var add_comment = comment_template(comment, true);
                    comment_element.children('ul.children').append(add_comment);
                    form.remove();
                    comment_element.children('.comment-body').find('dcomment-replay-button').removeClass('active')
                }
            }
        });
    });


    // Rating
    $(document).on('click', '.dcomment-add-rating', function(e) {
        e.preventDefault();

        var comment_block = $(this).closest('.comment-body');
        rating(comment_block, 'add');
    });

    $(document).on('click', '.dcomment-remove-rating', function(e) {
        e.preventDefault();

        var comment_block = $(this).closest('.comment-body');
        rating(comment_block, 'remove');
    });

    function rating(comment_block, rating) {
        var id = comment_block.attr('data-id');
        $.ajax({
            type: 'POST',
            url: "/comment/rating",
            data: {
                rating : rating,
                comment_id: id
            },
            success: function(json){
                var data = $.parseJSON(json);

                if (data['status'] == 1 && data['rating']) {
                    comment_block.children('.comment-menu').find('.dcomment-rating').html(data['rating']);
                } else {
                    alert(data['error']);
                }

            }
        });
    }

    // User
    $(document).on('submit', '.registration', function(e) {
        e.preventDefault();
        var data = $(this).serialize();

        $.ajax({
            type: 'POST',
            url: "/user/registration",
            data: data,
            success: function(json){
                var data = $.parseJSON(json);
                if (data['status'] == 1) {
                    alert('registration success');
                    $('.login-block').show();
                    $('.registration-block').hide();
                    $('.show-registration').remove(); // )
                } else {
                    alert(data['message']);
                }
            }
        });
    });

    $(document).on('submit', '.login', function(e) {
        e.preventDefault();
        var data = $(this).serialize();

        $.ajax({
            type: 'POST',
            url: "/user/login",
            data: data,
            success: function(json){
                var data = $.parseJSON(json);
                console.log(json);
                if (data['status'] == '1') {
                    comments_load();
                    $('.login-block').hide();
                    $('.show-login').parent('div').html('<a href="/user/logout">Logout</a>');
                    alert('Login success');
                } else {
                    alert(data['message']);
                }
            }
        });
    });

    $('.show-login').on('click', function(e){
        e.preventDefault();
        $('.login-block').show();
        $('.registration-block').hide();

    });

    $('.show-registration').on('click', function(e){
        e.preventDefault();
        $('.registration-block').show();
        $('.login-block').hide();
    });
});