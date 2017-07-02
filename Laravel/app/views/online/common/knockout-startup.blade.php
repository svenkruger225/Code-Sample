@if (!empty($startup) && $startup == 'notinuse')
    <script src="/onlinecourses/src/app/require.config.js"></script>
    <script data-main="app/startup" src="/onlinecourses/src/bower_modules/requirejs/require.js"></script>
@endif