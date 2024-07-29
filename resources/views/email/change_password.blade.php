@extends('email.header')
@section('email-content')

<table
align="center"
border="0"
      style="
        font-size: 13px;
        line-height: 22px;
        margin: 0 auto;
        width: 600px;
        background: #f4f5fd;
        background-color: #f4f5fd;
        text-align: center;
        padding: 25px;
      "
    >
      <tbody>
        <tr>
          <td
            style="
              font-family: 'Droid Sans', 'Helvetica Neue', Arial, sans-serif;
              font-size: 36px;
              padding: 0px 0px 15px 0px;
            "
          >
          Hi {{ $datas['user_full_name'] }}

          </td>
        </tr>
        <tr>
          <td
            style="
              font-family: 'Droid Sans', 'Helvetica Neue', Arial, sans-serif;
              font-size: 16px;
              padding: 0px 0px 10px 0px;
            "
          >
          You have successfully changed your password at Elroi Consumer portal.<br>  Please click below button to login.

        </td>
        </tr>
        <tr>
          <td style="
        border-style: none;
        border-width: 0;
        padding: 0;
        text-align: center;
        vertical-align: middle;
      ">
              <table border="0" cellpadding="0" cellspacing="0" style="
            border: 0;
            font-size: 15px;
            line-height: 22px;
            margin: 0 auto;
          ">
                  <tbody>
                      <tr>
                          <td style="
                  clear: both;
                  color: #ffffff;
                  opacity: 1;
                  padding: 12px;
                  text-align: center;
                  text-decoration: none;
                  background-color: #064e89;
                  background: #064e89;
                  width: 100px;
                  line-height: 18px;
                ">
                              <a href="{{$datas['login_url']}}" style=" background-color: #064e89;
                border: none;
                border-radius: 4px;
                padding: 10px 25px;
                color: #ffffff;
                text-align: center;
                text-decoration: none;">Login
                              </a>
                          </td>
                      </tr>
                  </tbody>
              </table>

          </td>
      </tr>
       
        <tr>
          <td
            style="
              font-family: 'Droid Sans', 'Helvetica Neue', Arial, sans-serif;
              font-size: 16px;
            "
          >
          Thank you, <br>Elroi Consumer Portal
        </td>
        </tr>
      </tbody>
    </table>

    @endsection