<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{setting('company_name')}} - Images</title>
    <!-- Bootstrap core CSS -->
    <link href="https://getbootstrap.com/docs/4.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="https://getbootstrap.com/docs/4.0/examples/grid/grid.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/viewerjs/1.3.6/viewer.min.css" rel="stylesheet" type="text/css" />
<style>
    .pictures {
        list-style: none;
        margin: 0;
        /* max-width: 30rem;*/
        padding: 0;
    }

    .pictures > li {
        border: 1px solid transparent;
        float: left;
        height: 250px;
        margin: 0 -1px -1px 0;
        overflow: hidden;
        width: calc(100% / 3);
    }

    .pictures > li > img {
        cursor: zoom-in;
        width: 100%;
        height: 100%;
    }
</style>
</head>
<body>
<div class="container">

    <div class="row">
        @if($result->images->isNotEmpty())
            <div class="col-lg-12 col-xl-12">
                        <ul id="image-view" class="pictures">
                            @foreach($result->images as $key => $value)
                                <li><img src="{{asset($value->path)}}" alt="{{$value->image_name}}"></li>
                            @endforeach
                        </ul>
                </div>
            </div>
        @endif
    </div> <!-- /container -->


</body>
<script src="https://cdnjs.cloudflare.com/ajax/libs/viewerjs/1.3.6/viewer.min.js"></script>
<script type="text/javascript">
    window.addEventListener('DOMContentLoaded', function () {
        var galley = document.getElementById('image-view');
        var viewer = new Viewer(galley, {
            url: 'data-original',
            title: function (image) {
                return image.alt + ' (' + (this.index + 1) + '/' + this.length + ')';
            },
        });
    });
</script>
</html>