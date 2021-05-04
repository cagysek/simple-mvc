
// metoda pro extraktování textu
String.prototype.extract = function(prefix, suffix) {
    s = this;
    var i = s.indexOf(prefix);
    if (i >= 0) {
        s = s.substring(i + prefix.length);
    }
    else {
        return '';
    }
    if (suffix) {
        i = s.indexOf(suffix);
        if (i >= 0) {
            s = s.substring(0, i);
        }
        else {
            return '';
        }
    }
    return s;
};

jQuery.extend( jQuery.fn.dataTableExt.oSort, {
    "formatted-num-pre": function ( a ) {

        // získám číslo mezi spany
        a = a.extract(">", "<");

        // odstraním mezery
        a = a.trim();

        a = (a === "-" || a === "") ? 0 : a;

        return parseFloat( a );
    },

    "formatted-num-asc": function ( a, b ) {
        return a - b;
    },

    "formatted-num-desc": function ( a, b ) {
        return b - a;
    }
} );


$(document).ready( function () {
    var cols = [];

    // načteme si th element, kde je nastavený colspan (počet sloupců pod názvem OKS)
    let taskCount = document.getElementById('task-count');

    if (!taskCount)
    {
        return;
    }

    // načteme si atribut colSpan
    taskCount = taskCount.colSpan;

    // vytvoření pole pro definici, kde budeme chtít aplikovat ten sort (podmnožina OKS)
    // začínáme od 4 (sloupec OK, chceme to sortovat jako číslo)
    // + 5 -> 5 prvních sloupců
    for(var i = 4 ; i < (taskCount + 5) ; i++)
    {
        cols.push(i);
    }

    $('#studenti_tab').DataTable( {
        "pageLength": 100,
        columnDefs: [
            { type: 'formatted-num', targets: cols }
        ]
    });
} );