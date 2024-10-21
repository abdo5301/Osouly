@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>

                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    You are logged in!
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Insert these scripts at the bottom of the HTML, but before you use any Firebase services -->

<!-- Firebase App (the core Firebase SDK) is always required and must be listed first -->
<script src="https://www.gstatic.com/firebasejs/8.1.2/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.1.2/firebase-messaging.js"></script>

<!-- If you enabled Analytics in your project, add the Firebase SDK for Analytics -->
<script src="https://www.gstatic.com/firebasejs/8.1.2/firebase-analytics.js"></script>

<!-- Add Firebase products that you want to use -->
<script src="https://www.gstatic.com/firebasejs/8.1.2/firebase-auth.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.1.2/firebase-firestore.js"></script>

<script>
    // are not available in the service worker.
     // TODO: Replace the following with your app's Firebase project configuration
    // For Firebase JavaScript SDK v7.20.0 and later, `measurementId` is an optional field
    var firebaseConfig = {
        apiKey: "AIzaSyCoBoup9wUA1v1VLtYs_asxB9RcTbUJ9so",
        authDomain: "osouly-9b405.firebaseapp.com",
        projectId: "osouly-9b405",
        storageBucket: "osouly-9b405.appspot.com",
        messagingSenderId: "148853246599",
        appId: "1:148853246599:web:edd6a885d387bad5154622"
    };

    // Initialize Firebase
    firebase.initializeApp(firebaseConfig);

    const messaging = firebase.messaging();
    messaging
        .requestPermission()
        .then(function () {

            console.log("Notification permission granted.");

            // get the token in the form of promise
            return messaging.getToken()
        })
        .then(function(token) {
            // print the token on the HTML page
            console.log("token is : " + token)
        })
        .catch(function (err) {
            console.log( err)
            console.log("Unable to get permission to notify.", err);
        });



</script>
@endsection
