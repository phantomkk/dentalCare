<!DOCTYPE html>
<html lang="en"><head>
    <title>Danh sách chi trả</title>
    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script type="text/javascript" src="/assets/user/bootstrap/bootstrap.js"></script>
    <script src="/assets/user/js/jquery-3.2.1.js"></script>
    <script src="/assets/user/js/jquery.easing.1.3.js"></script>


    <link rel="stylesheet" href="/assets/user/js/jquery.fancybox.css" />
    <script src="/assets/user/js/jquery.fancybox.js"></script>

    <script type="text/javascript" src="/assets/user/js/myjs.js"></script>
    <link href="https://fonts.googleapis.com/css?family=Italianno|Open+Sans:300,400,600,700,800&amp;subset=vietnamese" rel="stylesheet">
    <link rel="stylesheet" href="/assets/user/bootstrap/bootstrap.css">
    <link rel="stylesheet" href="/assets/user/bootstrap/bootstrap2.css">
    <!-- <link rel="stylesheet" href="http://gsgd.co.uk/sandbox/jquery/easing/jquery.easing.1.3.js"> -->
    <link rel="stylesheet" href="/assets/user/bootstrap/font-awesome.css">
    <link rel="stylesheet" href="/assets/user/css/mycss.css">

</head>
<body>
    <!-- start menu -->
<nav class="navbar navbar-light     bg-faded thanhmenu">
    <div class="container">
        <button class="navbar-toggler hidden-sm-up float-xs-right" type="button" data-toggle="collapse"
                data-target="#navmn">
        </button>

        <div class="collapse navbar-toggleable-xs" id="navmn">
            <!-- <a class="navbar-brand logo" href="#"><img src="images/icon/logo.png" alt=""></a> -->
            <ul class="nav navbar-nav float-sm-right">

                <li class="nav-item active">
                    <a class="nav-link " href="/gioithieu">Giới Thiệu</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link  " href="/doctorList">Chuyên Gia</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link  " href="/event">Sự kiện</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link  " href="/banggia">Bảng giá</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link  " href="/gioithieu">Liên hệ</a>
                </li>
                <li class="nav-item">

                @if(Session::has('currentUser'))
                    <li class="nav-item dropdown ">
                        <a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown">
                            <img src="{{Session::get('currentPatient')->avatar}}" class="user-image img-circle" alt="User Image"
                                 class="img-fluid img-responsive" style="max-height: 25px;">

                        </a>
                        <ul class="dropdown-menu"
                            style="position: absolute;right: 0;left: auto;background-color: whitesmoke">
                            <!-- User image -->

                            <li class="user-header">
                                <div class="container" style=";padding:10px 0px; ">
                                    <div class="row">
                                        <div class="col-sm-4 hoverImg" style="float: left;padding-left: 20px;">
                                            <img src="{{Session::get('currentPatient')->avatar}}"
                                                 class="img-circle img-responsive img-fluid borderImg "  id="divAcc1" alt="User Image"   width="50px;">
                                        </div>
                                        @foreach(\Session::get('listPatient') as $key => $value)
                                        <div class="col-sm-2"  >

                                            <img src="{{ $value->avatar }}"
                                                 class="img-circle img-responsive img-fluid" alt="User Image"  id="{!! $value->id !!}" width="50px;" onclick="changeInfo(this.id)">
                                        </div>
                                        @endforeach
                                    </div>
                                </div>

                            </li>
                            <li class="user-header" id="acc1" style="display: block">
                                <p>

                                   {{Session::get('currentPatient')->name}}
                                </p>
                            </li>
                            <li class="user-header" id="acc2" style="display: none">
                                <p>
                                    @foreach(\Session::get('listPatient') as $key)
                                        @if($key->id != Session::get('currentPatient')->id )
                                        <h1>{{$key->name}}</h1>
                                        @endif

                                    @endforeach
                                </p>
                            </li>
                                 <hr>

                            <li class="a-hover">
                                <a href="/lich-su-benh-an">Lịch sử khám bệnh</a>
                            </li>
                            <li class="gachngang"></li>
                            <li class="  a-hover">
                                <a href="/danh-sach-chi-tra"><span>Danh sách chi trả</span></a>
                            </li>
                            <li class="gachngang"></li>
                            <li class=" a-hover">
                                <a href="#"><span>Lịch hẹn</span></a>
                            </li>
                              <li class="gachngang"></li>

                            <!-- Menu Body -->
                            <!-- Menu Footer-->
                            <li class="user-footer" style="background-color: whitesmoke;padding-top: 5px;">

                                <div class="pull-left" style="padding-left: 1em;">
                                    <a href="/myProfile" class="btn btn-success btn-flat">Profile</a>
                                </div>
                                <div class="pull-right" style="padding-right: 1em;">
                                    <a href="/signOut" class="btn btn-success btn-flat">Sign out</a>
                                </div>

                            </li>
                        </ul>
                    </li>

                @else
                    <li class="nav-item dropdown ">
                        <a href="#" class="dropdown-toggle nav-link" data-toggle="dropdown" id="buttonLogin">
                            {{--<img src="assets/images/icon/user.jpg" class="user-image img-circle" alt="User Image"--}}
                            {{--class="img-fluid img-responsive" style="max-height: 25px;">--}}
                            Đăng Nhập
                        </a>
                        <ul class="dropdown-menu" id="drop"
                            style="position: absolute;right: 0;left: auto;background-color: whitesmoke;">
                            <!-- User image -->
                            <li class="user-header">
                                Đăng nhập

                            </li>
                            <!-- Menu Body -->

                            <!-- Menu Footer-->
                            <li class="user-footer" style="background-color: whitesmoke">
                                <div class="col-ms-12 col-md-offset-12">
                                    <div class="panel panel-default">
                                        <div class="panel-body" style="padding-left: 0.5em;padding-right: 0.5em;">

                                            <form action="{!! url('/loginUser') !!}" method="Post">
                                                {{ csrf_field() }}
                                                <div class="form-group has-feedback {{ $errors->has('phone') ? ' has-error' : '' }}">
                                                    <input type="text" class="form-control" placeholder="Phone" name="phone" value="{{ old('phone') }}"
                                                           required autofocus>
                                                    <span class="glyphicon glyphicon-phone form-control-feedback"></span>
                                                    @if ($errors->has('phone'))
                                                        <span class="help-block">
                                                            <strong>{{ $errors->first('phone') }} </strong>
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="form-group has-feedback">
                                                    <input type="password" class="form-control" placeholder="Password" name="password" required>
                                                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                                                    @if ($errors->has('password'))
                                                        <span class="help-block">
                                                            <strong>{{ $errors->first('password') }}</strong>
                                                        </span>
                                                    @endif
                                                </div>
                                                @if (\Session::has('fail'))
                                                    <span class="help-block has-error" style="color: #dd4b39">
                                                       <strong>{!! \Session::get('fail') !!} </strong>
                                                </span>
                                                @endif
                                                <div class="row">
                                                    <!-- /.col -->
                                                    <div class="col-xs-12">
                                                        <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
                                                    </div>
                                                    <!-- /.col -->

                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </li>
                    @endif
                    </li>



            </ul>
        </div>
    </div>
</nav>

    <!-- end menu -->
<div class="container" >
    <div class="row" style="background: url(/assets/images/banggia.jpg);height: 7em;">
        <div class="col-sm-8 push-sm-2 text-xs-center Bacsititle" >
            <h1 style="margin-top: 0.8em;color: white"><strong>Danh sách chi trả</strong></h1>
        </div>
    </div>


</div>
<div class="container">
    <br />
    <br />
    <div class="panel-group" id="accordion">
        @foreach($listPayment as $Payment)
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                   <div class="container">
                       <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapse{{$Payment->id}}"><div class="container">
                               <div class="col-sm-4">Ngày tạo: {{$Payment->created_at}}</div>
                               <div class="col-sm-4">Chi phí : {{$Payment->total_price}} VNĐ</div>
                               <div class="col-sm-4">Đã thu : {{$Payment->prepaid}} VNĐ</div>
                               <div class="col-sm-4">Trạng Thái :
                               @if($Payment->is_done == 0)
                                   hoàn thành
                               @else
                                   chưa hoàn thành
                                   @endif
                               </div>
                               <div>
                                   <form id="payment-form" role="form" action="{!! route('paypal') !!}" method="POST">
                                       <input type="hidden" name="amount" value="{{ $Payment->total_price - $Payment->prepaid }}">
                                       <input type="hidden" name="payment_id" value="{{ $Payment->id}}">
                                   <button type="submit">Paypal Payment</button>
                                   </form>
                               </div>
                           </div></a>
                   </div>

                </h4>
            </div>

            <div id="collapse{{$Payment->id}}" class="panel-collapse collapse in">
                <div class="panel-body">
                <table>
                    <tr>
                        <th>Ngày thực hiện </th>
                        <th>Số tiền chi trả : </th>
                        <th>Người thu : </th>
                    </tr>
                    @foreach($Payment->payment_details as $paymentDetail)
                    <tr>
                        <th>{{$paymentDetail->date_create}}</th>
                        <th>{{$paymentDetail->received_money}} VNĐ</th>
                        <th>{{$paymentDetail->staff->name}}</th>
                    </tr>
                    @endforeach


                </table>
                </div>
            </div>

        </div>
        @endforeach
    </div>
</div>
<div class="footer" style="background: url(/assets/images/HomePage/backgroundfooter.jpg);margin-top: 30px;">
    <div class="contact" id="contact">
        <div class="container">
            <div class="row">
                <div class="col-sm-4">
                    <div><img src="/assets/images/HomePage/logo.png" alt=""></div><br>
                    <div>Website: <a href="https://google.com.vn">projectcapstone.vn</a></div>
                    <div>Bác sĩ tư vấn (24/7) : <a class="zalovb" id="callme" onclick="ga('send', 'event', 'Phone', 'Click', 'Hotline');" href="tel:0968999777" rel="nofollow">1900.7979</a></div>
                </div>
                <div class="col-sm-4">
                    <div class="place">Hà Nội</div>
                    <div>"Tel:"
                        <a id="callme" onclick="ga('send', 'event', 'Phone', 'Click', 'Hotline');" href="tel:02473006466">024.73.00.64.66</a><br>
                        Mobile:<a class="zalovb" id="callme" onclick="ga('send', 'event', 'Phone', 'Click', 'Hotline');" href="tel:0968999777" rel="nofollow">0968.999.777</a><hr>
                    </div>
                    <div>
                        <a class="chidan" rel="nofollow" target="_blank" href="https://www.google.com/maps/place/B%E1%BB%87nh+Vi%E1%BB%87n+Th%E1%BA%A9m+M%E1%BB%B9+Kangnam/@21.000406,105.8297107,17z/data=!4m13!1m7!3m6!1s0x3135ac8758878c1f:0x8a59d1e808ee7392!2zMTkwIFRyxrDhu51uZyBDaGluaCwgS2jGsMahbmcgVGjGsOG7o25nLCDEkOG7kW5nIMSQYSwgSMOgIE7hu5lpLCBWaeG7h3QgTmFt!3b1!8m2!3d21.002795!4d105.828991!3m4!1s0x3135ac7d0b3e238b:0xdf9ae7b52fbbdef!8m2!3d21.0003971!4d105.8305875">190 Trường Chinh - Đống Đa - Hà Nội</a>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="place">Hồ Chí Minh</div>
                    <div>"Tel:"
                        <a id="callme" onclick="ga('send', 'event', 'Phone', 'Click', 'Hotline');" href="tel:02473006466">024.73.00.64.66</a><br>
                        Mobile:<a class="zalovb" id="callme" onclick="ga('send', 'event', 'Phone', 'Click', 'Hotline');" href="tel:0968999777" rel="nofollow">0968.999.777</a><hr>
                    </div>
                    <div>
                        <a class="chidan" rel="nofollow" target="_blank" href="https://www.google.com/maps/place/B%E1%BB%87nh+Vi%E1%BB%87n+Th%E1%BA%A9m+M%E1%BB%B9+Kangnam/@21.000406,105.8297107,17z/data=!4m13!1m7!3m6!1s0x3135ac8758878c1f:0x8a59d1e808ee7392!2zMTkwIFRyxrDhu51uZyBDaGluaCwgS2jGsMahbmcgVGjGsOG7o25nLCDEkOG7kW5nIMSQYSwgSMOgIE7hu5lpLCBWaeG7h3QgTmFt!3b1!8m2!3d21.002795!4d105.828991!3m4!1s0x3135ac7d0b3e238b:0xdf9ae7b52fbbdef!8m2!3d21.0003971!4d105.8305875">190 Trường Chinh - Quận 12 - TP.Hồ Chí Minh</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container" style="background-color: black;width: 100%">
        <div class="row">
            <div class="col-xs-12">
                <p>Designed by Phuc. All Rights Reserved</p>
            </div>
        </div>
    </div></div>
<!-- end footer -->
</body>
</html>
<script src="https://datatables.yajrabox.com/js/jquery.dataTables.min.js"></script>
<script src="https://datatables.yajrabox.com/js/datatables.bootstrap.js"></script>
<script type="text/javascript">
function changeInfo(id) {
        var Chooseid = id;


        $.ajax({
            url: 'changeCP/' + Chooseid,
            type:'GET',
            success: function(result){
                location.reload();
            } ,error: function (data) {
                alert(data);
            }
        });
    }
</script>