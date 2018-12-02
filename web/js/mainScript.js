
$(".faculty").hide();

$(document).ready(function() {

    $('[data-toggle="tooltip"]').tooltip();

    $(".facultyButton").on("click", function() {
        var facultyButtonId = $(this).attr('id');
        var facultyId = facultyButtonId.substring(0, facultyButtonId.lastIndexOf('F'));

        $("#" + facultyId).toggle();
        $(this).toggleClass("btn-success btn-danger");
    });

    $("td :checkbox").on("click", function() {
        var ectsSum = parseInt($("#ects").text());
        var ects = parseInt($(this).closest("tr").find('td:eq(4)').text());
        if($(this).prop( "checked")) ectsSum += ects;
        else ectsSum -= ects;
        $("#ects").text(ectsSum + " ECTS");
        $(this).closest('tr').toggleClass('bg-info');
    });

    function updateTextArea() {
        var allVals = "";

            $('td :checkbox').each(function () {
                if($(this).prop("checked")) allVals += ($(this).closest("tr").find('td:eq(2)').text() + ", " + $(this).closest("tr").find('td:eq(1)').text() + ", " + $(this).closest("tr").find('td:eq(4)').text() + ";\n");
            });
        $('.t').text(allVals);
        $('#t_ects').text(parseInt($("#ects").text()));
    }
    $(function() {
        $('tr input').click(updateTextArea);
        updateTextArea();
    });

    $('#myModal').on('shown.bs.modal', function () {
        $('#myInput').focus()
    })

    $("#previousButton").on("click", function() {
        $("#previous").toggle();
        $(this).toggleClass("btn-success btn-danger");
    });

    $("#currentButton").on("click", function() {
        $("#current").toggle();
        $(this).toggleClass("btn-success btn-danger");
    });

    $("#nextButton").on("click", function() {
        $("#next").toggle();
        $(this).toggleClass("btn-success btn-danger");
    });

    $("#otherButton").on("click", function() {
        $("#other").toggle();
        $(this).toggleClass("btn-success btn-danger");
    });

    $(".previousButton").on("click", function() {
        var id = $(this).attr('id').replace("previousButtonW", '');
        $("#previousW" + id).toggle();
        $(this).toggleClass("btn-success btn-danger");
    });

    $(".currentButton").on("click", function() {
        var id = $(this).attr('id').replace("currentButtonW", '');
        $("#currentW" + id).toggle();
        $(this).toggleClass("btn-success btn-danger");
    });

    $(".nextButton").on("click", function() {
        var id = $(this).attr('id').replace("nextButtonW", '');
        $("#nextW" + id).toggle();
        $(this).toggleClass("btn-success btn-danger");
    });

    $(".otherButton").on("click", function() {
        var id = $(this).attr('id').replace("otherButtonW", '');
        $("#otherW" + id).toggle();
        $(this).toggleClass("btn-success btn-danger");
    });

    $(".coursesSearchedToggleable").toggle();
    $(".coursesStudentsToggleable").toggle();

    $("#coursesListToggle").on("change", function(){
       $("#coursesList").toggle("slow");
       $("#coursesSearchedStudents").toggleClass("col-md-5 col-md-12");

        $(".coursesSearchedToggleable").toggle();
        $(".coursesStudentsToggleable").toggle();
    });

    $("#coursesSearchedToggle").on("change", function(){
        $("#searchInput").toggle("slow");
        $("#searchedCoursesTable").toggle("slow");

        if($(this).prop("checked") === false && $("#coursesStudentsToggle").prop("checked") === false) {

            $("#coursesList").removeClass("col-md-7").addClass("col-md-12");
        } else {

            $("#coursesList").removeClass("col-md-12").addClass("col-md-7");
        }
    });

    $("#coursesStudentsToggle").on("change", function(){
        $("#studentsCoursesTable").toggle("slow");

        if($(this).prop("checked") === false && $("#coursesSearchedToggle").prop("checked") === false) {

            $("#coursesList").removeClass("col-md-7").addClass("col-md-12");
        } else {

            $("#coursesList").removeClass("col-md-12").addClass("col-md-7");
        }
    });

    $("#searchInput").on("input", function() {
        var searchedCoursesHeight = $("#searchedCoursesTable").height();
        var studentsCoursesHeight = $("#studentsCoursesTable").height();
        var heightSum = searchedCoursesHeight + studentsCoursesHeight;
        var heightMax = window.innerHeight * 0.92;
        console.log("stud " + studentsCoursesHeight);
        console.log("search " + searchedCoursesHeight);
        console.log("sum " + heightSum);
        console.log("max " + heightMax);

        if(heightSum >= heightMax){

            $("#studentsCoursesTable").height("46vh");
            $("#searchedCoursesTable").height("46vh");
        }
    });

    $("td :checkbox").on("click", function() {

        var coursCode = $(this).closest("tr").find('td:eq(1)').text();
        if($(this).prop('checked') == true && $('#selectedCourses').children('tr:has(td:contains(' + coursCode + '))').length == 0){

            var courseAsArray = [];
            var numberOfTD = $(this).closest("tr").children('td').length - 2;
            for(var i = 1; i <= numberOfTD; i++){

                courseAsArray[i - 1] = $(this).closest("tr").find('td:eq(' + i + ')').text();
            }

            $('#selectedCourses').append('<tr></tr>');
            var numberOrRows = $('#selectedCourses').children('tr').length;

            var courseRow = $('#selectedCourses tr:last');
            courseRow.append('<td>' + numberOrRows + '</td>');
            for(var i = 0; i < numberOfTD; i++){

                courseRow.append('<td>' + courseAsArray[i] + '</td>');
            }
        } else {

            var selectedCourses = $('#selectedCourses');
            var coursCode = $(this).closest("tr").find('td:eq(1)').text();
            var selectedCourseRow = $('#selectedCourses').find('tr:has(td:contains(' + coursCode + '))');
            selectedCourseRow.remove();

            var numberOfRows = selectedCourses.children('tr').length;

            for(var i = 0; i < numberOfRows; i++){

                selectedCourses.find('tr:eq(' + i +')').find('td:eq(0)').text(i + 1);
            }
        }
    });

});
