$(function() {

    console.log(window.location.href);

    $('#dcomment-block').html('').addClass('loader');
    $.ajax({
        type: 'POST',
        url: "/comment/page",
        data: {
            url: window.location.href
        },
        success: function(json){
            var data = $.parseJSON(json);
            var comments = commentsTree(data, 0);
            $('#dcomment-block').html(comments).removeClass('loader');
        }
    });

    function commentsTree(data, parent_id){
        if(data[parent_id]){
            console.log(1);
            var tree = '<ul>';
            for(var item in data[parent_id]) {
                //console.log(data[item]);
                tree += '<li><p>' + data[parent_id][item]['message'] + ' #' + data[parent_id][item]['id']  + data[parent_id][item]['date'] + '</p>';
                tree +=  commentsTree(data, data[parent_id][item]['id']);
                tree += '</li>';
            }
            tree += '</ul>';
        } else return '';
        return tree;
    }
});