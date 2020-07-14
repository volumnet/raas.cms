export default function(change_query, include_dirs, initial_path) {
    if (!initial_path) {
        initial_path = document.location.href
    }
    if (change_query.substr(0, 1) == '?') {
        change_query = change_query.substr(1);
    }
    var query_dir = initial_path.split('?').slice(0, 1).toString();
    var query_str = initial_path.split('?').slice(1).toString();
    
    var old_query = query_str.split('&');
    var change = change_query.split('&');
    
    var query = {};
    var temp = [];
    
    var new_query = [];
    for (i = 0; i < old_query.length; i++) {
        temp = old_query[i].split('=');
        if (temp[0].length > 0) {
            query[temp[0]] = temp[1];
        }
    }
    for (i = 0; i < change.length; i++) {
        temp = change[i].split('=');
        if (temp[0].length > 0) {
            query[temp[0]] = temp[1];
        }
    }
    temp = [];
    for (key in query) {
        if (query[key] && (query[key].length > 0)) {
            temp[temp.length] = key + '=' + query[key];
        }
    }
    query = temp.join('&');
    return query;
};