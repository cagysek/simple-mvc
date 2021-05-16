
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

$.extend( $.fn.dataTableExt.oSort, {
    "formatted-num-pre": function ( a ) {
        // získám číslo mezi spany
        a = a.extract(">", "<");

        // odstraním mezery
        a = a.trim();

        a = parseFloat(a);

        // pokud není číslo, dám 0
        if (isNaN(a))
        {
            a = 0;
        }

        return a;
    },

    "formatted-num-asc": function ( a, b ) {
        return a - b;
    },

    "formatted-num-desc": function ( a, b ) {
        return b - a;
    }
} );

// https://datatables.net/plug-ins/sorting/czech-string
$.extend( $.fn.dataTableExt.oSort, {
    "czech-pre": function ( a ) {
        var special_letters = {
            "A": "Aa", "a": "aa", "Á": "Ab", "á": "ab",
            "C": "Ca", "c": "ca", "Č": "Cb", "č": "cb",
            "D": "Da", "d": "da", "Ď": "db", "ď": "db",
            "E": "Ea", "e": "ea", "É": "eb", "é": "eb", "Ě": "Ec", "ě": "ec",
            "I": "Ia", "i": "ia", "Í": "Ib", "í": "ib",
            "N": "Na", "n": "na", "Ň": "Nb", "ň": "nb",
            "O": "Oa", "o": "oa", "Ó": "Ob", "ó": "ob",
            "R": "Ra", "r": "ra", "Ř": "Rb", "ř": "rb",
            "S": "Sa", "s": "sa", "Š": "Sb", "š": "sb",
            "T": "Ta", "t": "ta", "Ť": "Tb", "ť": "tb",
            "U": "Ua", "u": "ua", "Ú": "Ub", "ú": "ub", "Ů": "Uc", "ů": "uc",
            "Y": "Ya", "y": "ya", "Ý": "Yb", "ý": "yb",
            "Z": "Za", "z": "za", "Ž": "Zb", "ž": "zb"
        };
        for (var val in special_letters)
            a = a.split(val).join(special_letters[val]).toLowerCase();
        return a;
    },

    "czech-asc": function ( a, b ) {
        return ((a < b) ? -1 : ((a > b) ? 1 : 0));
    },

    "czech-desc": function ( a, b ) {
        return ((a < b) ? 1 : ((a > b) ? -1 : 0));
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

    var t = $('#studenti_tab').DataTable( {
        "pageLength": 100,
        columnDefs: [
            { type: 'formatted-num', targets: cols },
            { type: 'czech', targets: [1,2] },
            {
                searchable: false,
                orderable: false,
                targets: 0
            }
        ],
        "order": [[ 1, 'asc' ]] // výchozí řazení
    });

    // reindexace nově seřezených řádek
    t.on( 'order.dt search.dt', function () {
        t.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
            cell.innerHTML = i+1;
        } );
    } ).draw();

} );