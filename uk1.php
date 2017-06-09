<html>
<head>
<script
  src="https://code.jquery.com/jquery-3.2.1.min.js"
  integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
  crossorigin="anonymous"></script>
</head>
<body>
<script>
$.ajax({
    url: 'https://api.instapay.kr/s1/p',
    type: 'DELETE',
    success: function(result) {
      alert(result);
    }
});
</script>
</body>
</html>
