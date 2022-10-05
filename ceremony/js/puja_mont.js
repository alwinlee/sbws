$(document).ready(function(){
    $.ajax({
        async: false,url: "top.php",
        success: function (data) {
            $("#pageTop").append(data);
        }
    });

    // 叫出班級報名表
    $('#query').click(function(){
        getclassMember("","","","","","");
    });

    //-----------------------------------------------------------------------------------------------------------------------------------
    // 傳送報名
    $('#send').click(function(){
        var tbname=$("#tb").val();
        var tbstatistic=$("#tbstatistic").val();
        var nMemberCnt=$("#memberCnt").val();
        var classid=$("#classid").val();
        var tb=$('.tb').val();
        var trafftb=$('.trafftb').val();
        var sub=$('.sub').val();
        var payitem=$('#payitem').val();
        var payercurid=$('#payerid').val();
        var payercurname=$('#payername').val();
        var curdate=$('.currentdate').val();//alert(curdate);
        var dbg=$('.dbg').val();
        var endjoin=$('.endjoin').val();
        var traffprice=$('#traffroundfee').val();

        if(classid==null||typeof classid==='undefined'||classid==""){alert("未取得班級資訊!");return ;}

        var clsid=$("#clsid").val();
        var clsname=$("#clsname").val();
        var clsfullname=$("#clsfullname").val();
        var clsarea=$("#clsarea").val();
        var clsregion=$("#clsregion").val();
        var clshasleader=$("#Major").val();
        if(clsid==null||typeof clsid==='undefined'||clsid==""){alert("未取得班級資訊!");return ;}
        if(clsname==null||typeof clsname==='undefined'||clsname==""){alert("未取得班級資訊!");return ;}
        if(clsarea==null||typeof clsarea==='undefined'||clsarea==""){alert("未取得班級資訊!");return ;}
        if(clsregion==null||typeof clsregion==='undefined'||clsregion==""){alert("未取得班級區域資訊!");return ;}
        if(clshasleader==null||typeof clshasleader==='undefined'||clshasleader==""){alert("未取得班級幹部資訊!");return ;}

        var bPaid=false; if($('.payitem').val()=="YES"){bPaid=true;}//是否要顯示繳費項目
        var bCancelReg1=false; //取消報名
        var bCancelReg2=false; //取消報名
        var bCancelReg3=false; //取消報名
        var bCancelReg4=false; //取消報名
        var bCancelReg5=false; //取消報名
        var bCancelReg6=false; //取消報名

        var allsqlcmd=""; var sqlcmd=""; var joinday1=0; var joinday2=0; var joinday3=0;
        var regdate1=""; var paydate1=""; var regnewdate1=""; var paynewdate1=""; var payernewid1=""; var payernewname1="";
        var regdate2=""; var paydate2=""; var regnewdate2=""; var paynewdate2=""; var payernewid2=""; var payernewname2="";
        var regdate3=""; var paydate3=""; var regnewdate3=""; var paynewdate3=""; var payernewid3=""; var payernewname3="";
        var regdate4=""; var paydate4=""; var regnewdate4=""; var paynewdate4=""; var payernewid4=""; var payernewname4="";
        var regdate5=""; var paydate5=""; var regnewdate5=""; var paynewdate5=""; var payernewid5=""; var payernewname5="";
        var regdate6=""; var paydate6=""; var regnewdate6=""; var paynewdate6=""; var payernewid6=""; var payernewname6="";
        var fee1=0; var fee2=0; var fee3=0;var fee4=0; var fee5=0; var fee6=0;

        var ojoinday1=0;var ojoinday2=0;var ojoinday3=0; var ojoinday4=0;var ojoinday5=0;var ojoinday6=0;
        var otraffic1="";var otraffic2="";var otraffic3="";var otraffic4="";var otraffic5="";var otraffic6="";
        var ocancel1="";var ocancel2="";var ocancel3=""; var ocancel4="";var ocancel5="";var ocancel6="";
        var ofee1=0;var ofee2=0;var ofee3=0;var ofee4=0;var ofee5=0;var ofee6=0;
        var clssqlcmd=",`classname`=&#&#"+clsname+"&#&#,`CLS_ID`=&#&#"+clsid+"&#&#,`area`=&#&#"+clsarea+"&#&#,`areaid`=&#&#"+clsregion+"&#&#,`classfullname`=&#&#"+clsfullname+"&#&# ";//更新班級資料
        var jointotal = 0;
        for(i=1;i<=nMemberCnt;i++)
        {
            bPay=false;
            lock=$('#idx_'+i).attr('lock');
            ojoinday1=$('#idx_'+i).attr('ojoinday1');otraffic1=$('#idx_'+i).attr('traffic1');otrafficround1=$('#idx_'+i).attr('trafficround1');ocancel1=$('#idx_'+i).attr('cancel1');ofee1=$('#idx_'+i).attr('fee1');
            ojoinday2=$('#idx_'+i).attr('ojoinday2');otraffic2=$('#idx_'+i).attr('traffic2');otrafficround2=$('#idx_'+i).attr('trafficround2');ocancel2=$('#idx_'+i).attr('cancel2');ofee2=$('#idx_'+i).attr('fee2');
            ojoinday3=$('#idx_'+i).attr('ojoinday3');otraffic3=$('#idx_'+i).attr('traffic3');otrafficround3=$('#idx_'+i).attr('trafficround3');ocancel3=$('#idx_'+i).attr('cancel3');ofee3=$('#idx_'+i).attr('fee3');
            ojoinday4=$('#idx_'+i).attr('ojoinday4');otraffic4=$('#idx_'+i).attr('traffic4');otrafficround4=$('#idx_'+i).attr('trafficround4');ocancel4=$('#idx_'+i).attr('cancel4');ofee4=$('#idx_'+i).attr('fee4');
            ojoinday5=$('#idx_'+i).attr('ojoinday5');otraffic5=$('#idx_'+i).attr('traffic5');otrafficround5=$('#idx_'+i).attr('trafficround5');ocancel5=$('#idx_'+i).attr('cancel5');ofee5=$('#idx_'+i).attr('fee5');
            ojoinday6=$('#idx_'+i).attr('ojoinday6');otraffic6=$('#idx_'+i).attr('traffic6');otrafficround6=$('#idx_'+i).attr('trafficround6');ocancel6=$('#idx_'+i).attr('cancel6');ofee6=$('#idx_'+i).attr('fee6');

            idx=$('#notjoin_'+i).attr('idx');//取得idx

            if(lock==1&&bPaid==false)//已經 lock且不是管理者無繳費確認功能=>不用處理此 record
            {
                txtMemo=$('#memo_'+i).val();//備註
                sqlcmd="UPDATE `"+tbname+"` "+"SET `memo`=&#&#"+txtMemo+"&#&#";
                sqlcmd+=clssqlcmd;
                sqlcmd+=" WHERE `idx`="+idx;
                allsqlcmd+=sqlcmd;allsqlcmd += ";;;;"; //allsqlcmd+="<br>";//debug
                continue;
            }

            regdate1=$('#idx_'+i).attr('regdate1');paydate1=$('#idx_'+i).attr('paydate1');payerid1=$('#idx_'+i).attr('payerid1');payername1=$('#idx_'+i).attr('payername1');
            regdate2=$('#idx_'+i).attr('regdate2');paydate2=$('#idx_'+i).attr('paydate2');payerid2=$('#idx_'+i).attr('payerid2');payername2=$('#idx_'+i).attr('payername2');
            regdate3=$('#idx_'+i).attr('regdate3');paydate3=$('#idx_'+i).attr('paydate3');payerid3=$('#idx_'+i).attr('payerid3');payername3=$('#idx_'+i).attr('payername3');
            regdate4=$('#idx_'+i).attr('regdate4');paydate4=$('#idx_'+i).attr('paydate4');payerid4=$('#idx_'+i).attr('payerid4');payername4=$('#idx_'+i).attr('payername4');
            regdate5=$('#idx_'+i).attr('regdate5');paydate5=$('#idx_'+i).attr('paydate5');payerid5=$('#idx_'+i).attr('payerid5');payername5=$('#idx_'+i).attr('payername5');
            regdate6=$('#idx_'+i).attr('regdate6');paydate6=$('#idx_'+i).attr('paydate6');payerid6=$('#idx_'+i).attr('payerid6');payername6=$('#idx_'+i).attr('payername6');

            if(bPaid==true){if($('#pay_'+i).is(':checked')){bPay=true;}}

            traff1="Z";traffReal1="Z"; traffround1="0";traffroundReal1="0";
            traff2="Z";traffReal2="Z"; traffround2="0";traffroundReal2="0";
            traff3="Z";traffReal3="Z"; traffround3="0";traffroundReal3="0";
            traff4="Z";traffReal4="Z"; traffround4="0";traffroundReal4="0";
            traff5="Z";traffReal5="Z"; traffround5="0";traffroundReal5="0";
            traff6="Z";traffReal6="Z"; traffround6="0";traffroundReal6="0";

            paid1=0;paid2=0;paid3=0;paid4=0;paid5=0;paid6=0;
            joinday1=0;joinday2=0;joinday3=0; joinday4=0;joinday5=0;joinday6=0;
            type=0;speccare=0;
            fami1=0;fami2=0; fami3=0;fami4=0; fami5=0;fami6=0;
            service1=0;service2=0;service3=0;service4=0;service5=0;service6=0;
            meal1=0;meal2=0;meal3=0;meal4=0;meal5=0;meal6=0;
            mealx1=0;mealx2=0;mealx3=0;mealx4=0;mealx5=0;mealx6=0;
            live1=0;live2=0;live3=0;live4=0;live5=0;live6=0;

            if($('#join1_'+i).is(':checked')){joinday1=1;traff1=$("#traffic1_"+i).val();traffround1=$("#trafficround1_"+i).val();if(traffround1== undefined){traffround1=0;}if(bPay){traffReal1=traff1;traffroundReal1=traffround1;}}
            if($('#join2_'+i).is(':checked')){joinday2=1;traff2=$("#traffic2_"+i).val();traffround2=$("#trafficround2_"+i).val();if(traffround2== undefined){traffround2=0;}if(bPay){traffReal2=traff2;traffroundReal2=traffround2;}}
            if($('#join3_'+i).is(':checked')){joinday3=1;traff3=$("#traffic3_"+i).val();traffround3=$("#trafficround3_"+i).val();if(traffround3== undefined){traffround3=0;}if(bPay){traffReal3=traff3;traffroundReal3=traffround3;}}
            if($('#join4_'+i).is(':checked')){joinday4=1;traff4=$("#traffic4_"+i).val();traffround4=$("#trafficround4_"+i).val();if(traffround4== undefined){traffround4=0;}if(bPay){traffReal4=traff4;traffroundReal4=traffround4;}}
            if($('#join5_'+i).is(':checked')){joinday5=1;traff5=$("#traffic5_"+i).val();traffround5=$("#trafficround5_"+i).val();if(traffround5== undefined){traffround5=0;}if(bPay){traffReal5=traff5;traffroundReal5=traffround5;}}
            if($('#join6_'+i).is(':checked')){joinday6=1;traff6=$("#traffic6_"+i).val();traffround6=$("#trafficround6_"+i).val();if(traffround6== undefined){traffround6=0;}if(bPay){traffReal6=traff6;traffroundReal6=traffround6;}}
            if (joinday1 > 0 || joinday2 > 0 || joinday3 > 0 || joinday4 > 0 || joinday5 > 0 || joinday6 > 0) { 
                jointotal += 1;
            }
            if($('#meal1_'+i).is(':checked')){meal1=1;}
            if($('#meal2_'+i).is(':checked')){meal2=1;}
            if($('#meal3_'+i).is(':checked')){meal3=1;}
            if($('#meal4_'+i).is(':checked')){meal4=1;}
            if($('#meal5_'+i).is(':checked')){meal5=1;}
            if($('#meal6_'+i).is(':checked')){meal6=1;}

            if($('#mealx1_'+i).is(':checked')){mealx1=1;}
            if($('#mealx2_'+i).is(':checked')){mealx2=1;}
            if($('#mealx3_'+i).is(':checked')){mealx3=1;}
            if($('#mealx4_'+i).is(':checked')){mealx4=1;}
            if($('#mealx5_'+i).is(':checked')){mealx5=1;}
            if($('#mealx6_'+i).is(':checked')){mealx6=1;}

            if($('#live1_'+i).is(':checked')){live1=1;}
            if($('#live2_'+i).is(':checked')){live2=1;}
            if($('#live3_'+i).is(':checked')){live3=1;}
            if($('#live4_'+i).is(':checked')){live4=1;}
            if($('#live5_'+i).is(':checked')){live5=1;}
            if($('#live6_'+i).is(':checked')){live6=1;}

            traffitem1=traff1+","+traffround1+","+traffReal1+","+traffroundReal1;
            traffitem2=traff2+","+traffround2+","+traffReal2+","+traffroundReal2;
            traffitem3=traff3+","+traffround3+","+traffReal3+","+traffroundReal3;
            traffitem4=traff4+","+traffround4+","+traffReal4+","+traffroundReal4;
            traffitem5=traff5+","+traffround5+","+traffReal5+","+traffroundReal5;
            traffitem6=traff6+","+traffround6+","+traffReal6+","+traffroundReal6;

            // 判斷是否取消報名
            bCancelReg1=false;
            if(joinday1==0){
                if(paydate1=="1970-01-01"||paydate1==""){bCancelReg1=false;}
                else{bCancelReg1=true;} //曾經報名繳費,但現在取消 => 後面有取用此值來決定是否註記
            }
            bCancelReg2=false;
            if(joinday2==0){
                if(paydate2=="1970-01-01"||paydate2==""){bCancelReg2=false;}
                else{bCancelReg2=true;} //曾經報名繳費,但現在取消 => 後面有取用此值來決定是否註記
            }
            bCancelReg3=false;
            if(joinday3==0){
                if(paydate3=="1970-01-01"||paydate3==""){bCancelReg3=false;}
                else{bCancelReg3=true;} //曾經報名繳費,但現在取消 => 後面有取用此值來決定是否註記
            }
            bCancelReg4=false;
            if(joinday4==0){
                if(paydate4=="1970-01-01"||paydate4==""){bCancelReg4=false;}
                else{bCancelReg4=true;} //曾經報名繳費,但現在取消 => 後面有取用此值來決定是否註記
            }
            bCancelReg5=false;
            if(joinday5==0){
                if(paydate5=="1970-01-01"||paydate5==""){bCancelReg5=false;}
                else{bCancelReg5=true;} //曾經報名繳費,但現在取消 => 後面有取用此值來決定是否註記
            }
            bCancelReg6=false;
            if(joinday6==0){
                if(paydate6=="1970-01-01"||paydate6==""){bCancelReg6=false;}
                else{bCancelReg6=true;} //曾經報名繳費,但現在取消 => 後面有取用此值來決定是否註記
            }

            // 考慮 reg date & pay date
            if(joinday1==0){regnewdate1="1970-01-01";paynewdate1="1970-01-01";}//未報名,時間重設為1970-01-01
            else{regnewdate1=regdate1;paynewdate1=paydate1;if(regdate1=="1970-01-01"||regdate1==""||regdate1=="0000-00-00"){regnewdate1=curdate;}}

            if(joinday2==0){regnewdate2="1970-01-01";paynewdate2="1970-01-01";}//未報名,時間重設為1970-01-01
            else{regnewdate2=regdate2;paynewdate2=paydate2;if(regdate2=="1970-01-01"||regdate2==""||regdate2=="0000-00-00"){regnewdate2=curdate;}}

            if(joinday3==0){regnewdate3="1970-01-01";paynewdate3="1970-01-01";}//未報名,時間重設為1970-01-01
            else{regnewdate3=regdate3;paynewdate3=paydate3;if(regdate3=="1970-01-01"||regdate3==""||regdate3=="0000-00-00"){regnewdate3=curdate;}}

            if(joinday4==0){regnewdate4="1970-01-01";paynewdate4="1970-01-01";}//未報名,時間重設為1970-01-01
            else{regnewdate4=regdate4;paynewdate4=paydate4;if(regdate4=="1970-01-01"||regdate4==""||regdate4=="0000-00-00"){regnewdate4=curdate;}}

            if(joinday5==0){regnewdate5="1970-01-01";paynewdate5="1970-01-01";}//未報名,時間重設為1970-01-01
            else{regnewdate5=regdate5;paynewdate5=paydate5;if(regdate5=="1970-01-01"||regdate5==""||regdate5=="0000-00-00"){regnewdate5=curdate;}}

            if(joinday6==0){regnewdate6="1970-01-01";paynewdate6="1970-01-01";}//未報名,時間重設為1970-01-01
            else{regnewdate6=regdate6;paynewdate6=paydate6;if(regdate6=="1970-01-01"||regdate6==""||regdate6=="0000-00-00"){regnewdate6=curdate;}}

            //fee=$('#fee_'+i).text();if(fee==""){fee=0;}// 車資
            fee1=getfeebyitem(i,1);// 車資1
            fee2=getfeebyitem(i,2);// 車資2
            fee3=getfeebyitem(i,3);// 車資2
            fee4=getfeebyitem(i,4);// 車資2
            fee5=getfeebyitem(i,5);// 車資2
            fee6=getfeebyitem(i,6);// 車資2

            //fee3=getfee3(i);// 車資3

            txtMemo=$('#memo_'+i).val();//備註

            //考慮 lock, paid, pay, cost if(bPay){paid=fee;}

            if(bPaid==true){//管理窗口
                lock=0;
                paid1=0;paid2=0;paid3=0;paid4=0;paid5=0;paid6=0;
                cancel1=0;cancel2=0;cancel3=0;cancel4=0;cancel5=0;cancel6=0; //註記取消報名
                if(bPay==true)
                {
                    lock=1;paid1=fee1;paid2=fee2; paid3=fee3;paid4=fee4;paid5=fee5; paid6=fee6;
                    if(paydate1=="1970-01-01"||paydate1==""){paynewdate1=curdate;}
                    if(payerid1==""){payernewid1=payercurid;}else{payernewid1=payerid1;}
                    if(payername1==""){payernewname1=payercurname;}else{payernewname1=payername1;}

                    if(paydate2=="1970-01-01"||paydate2==""){paynewdate2=curdate;}
                    if(payerid2==""){payernewid2=payercurid;}else{payernewid2=payerid2;}
                    if(payername2==""){payernewname2=payercurname;}else{payernewname2=payername1;}

                    if(paydate3=="1970-01-01"||paydate3==""){paynewdate3=curdate;}
                    if(payerid3==""){payernewid3=payercurid;}else{payernewid3=payerid3;}
                    if(payername3==""){payernewname3=payercurname;}else{payernewname3=payername1;}

                    if(paydate4=="1970-01-01"||paydate4==""){paynewdate4=curdate;}
                    if(payerid4==""){payernewid4=payercurid;}else{payernewid4=payerid4;}
                    if(payername4==""){payernewname4=payercurname;}else{payernewname4=payername1;}

                    if(paydate5=="1970-01-01"||paydate5==""){paynewdate5=curdate;}
                    if(payerid5==""){payernewid5=payercurid;}else{payernewid5=payerid5;}
                    if(payername5==""){payernewname5=payercurname;}else{payernewname5=payername1;}

                    if(paydate6=="1970-01-01"||paydate6==""){paynewdate6=curdate;}
                    if(payerid6==""){payernewid6=payercurid;}else{payernewid6=payerid6;}
                    if(payername6==""){payernewname6=payercurname;}else{payernewname6=payername1;}
                }else{
                    //paid1=0;paid2=0;paid2=0;
                    paynewdate1="1970-01-01";paynewdate2="1970-01-01";paynewdate3="1970-01-01";paynewdate4="1970-01-01";paynewdate5="1970-01-01";paynewdate6="1970-01-01";
                    payernewid1="";payernewid2="";payernewid3="";payernewid4="";payernewid5="";payernewid6="";
                    payernewname1="";payernewname2="";payernewname3="";payernewname4="";payernewname5="";payernewname6="";
                }
                cancelinfo1="";cancelinfo2="";cancelinfo3="";cancelinfo4="";cancelinfo5="";cancelinfo6="";
                if(bCancelReg1==true&&endjoin=="YES"){cancel1=ojoinday1;cancelinfo1=ojoinday1+"#"+otraffic1+"#"+ofee1+"#"+payername1+"#"+paydate1+"#"+curdate+"#"+payercurname+"#"+payercurid+"#";}
                if(bCancelReg2==true&&endjoin=="YES"){cancel2=ojoinday2;cancelinfo2=ojoinday2+"#"+otraffic2+"#"+ofee2+"#"+payername2+"#"+paydate2+"#"+curdate+"#"+payercurname+"#"+payercurid+"#";}
                if(bCancelReg3==true&&endjoin=="YES"){cancel3=ojoinday3;cancelinfo3=ojoinday3+"#"+otraffic3+"#"+ofee3+"#"+payername3+"#"+paydate3+"#"+curdate+"#"+payercurname+"#"+payercurid+"#";}
                if(bCancelReg4==true&&endjoin=="YES"){cancel4=ojoinday4;cancelinfo4=ojoinday4+"#"+otraffic4+"#"+ofee4+"#"+payername4+"#"+paydate4+"#"+curdate+"#"+payercurname+"#"+payercurid+"#";}
                if(bCancelReg5==true&&endjoin=="YES"){cancel5=ojoinday5;cancelinfo5=ojoinday5+"#"+otraffic5+"#"+ofee5+"#"+payername5+"#"+paydate5+"#"+curdate+"#"+payercurname+"#"+payercurid+"#";}
                if(bCancelReg6==true&&endjoin=="YES"){cancel6=ojoinday6;cancelinfo6=ojoinday6+"#"+otraffic6+"#"+ofee6+"#"+payername6+"#"+paydate6+"#"+curdate+"#"+payercurname+"#"+payercurid+"#";}

                sqlcmd="UPDATE `"+tbname+"` "+"SET ";
                sqlcmd+="`day1`="+joinday1+",`meal1`="+meal1+",`specialcase1`="+mealx1+",`attend1`="+live1+",`traff1`=&#&#"+traffitem1+"&#&#,`cost1`="+fee1+",`lock1`="+lock+",`pay1`="+paid1+",`regdate1`=&#&#"+regnewdate1+"&#&#,";
                sqlcmd+="`paydate1`=&#&#"+paynewdate1+"&#&#,`paybyid1`=&#&#"+payernewid1+"&#&#,`paybyname1`=&#&#"+payernewname1+"&#&#,";

                sqlcmd+="`day2`="+joinday2+",`meal2`="+meal2+",`specialcase2`="+mealx2+",`attend2`="+live2+",`traff2`=&#&#"+traffitem2+"&#&#,`cost2`="+fee2+",`lock2`="+lock+",`pay2`="+paid2+",`regdate2`=&#&#"+regnewdate2+"&#&#,";
                sqlcmd+="`paydate2`=&#&#"+paynewdate2+"&#&#,`paybyid2`=&#&#"+payernewid2+"&#&#,`paybyname2`=&#&#"+payernewname2+"&#&#,";

                sqlcmd+="`day3`="+joinday3+",`meal3`="+meal3+",`specialcase3`="+mealx3+",`attend3`="+live3+",`traff3`=&#&#"+traffitem3+"&#&#,`cost3`="+fee3+",`lock3`="+lock+",`pay3`="+paid3+",`regdate3`=&#&#"+regnewdate3+"&#&#,";
                sqlcmd+="`paydate3`=&#&#"+paynewdate3+"&#&#,`paybyid3`=&#&#"+payernewid3+"&#&#,`paybyname3`=&#&#"+payernewname3+"&#&#,";

                sqlcmd+="`day4`="+joinday4+",`meal4`="+meal4+",`specialcase4`="+mealx4+",`attend4`="+live4+",`traff4`=&#&#"+traffitem4+"&#&#,`cost4`="+fee4+",`lock4`="+lock+",`pay4`="+paid4+",`regdate4`=&#&#"+regnewdate4+"&#&#,";
                sqlcmd+="`paydate4`=&#&#"+paynewdate4+"&#&#,`paybyid4`=&#&#"+payernewid4+"&#&#,`paybyname4`=&#&#"+payernewname4+"&#&#,";

                sqlcmd+="`day5`="+joinday5+",`meal5`="+meal5+",`specialcase5`="+mealx5+",`attend5`="+live5+",`traff5`=&#&#"+traffitem5+"&#&#,`cost5`="+fee5+",`lock5`="+lock+",`pay5`="+paid5+",`regdate5`=&#&#"+regnewdate5+"&#&#,";
                sqlcmd+="`paydate5`=&#&#"+paynewdate5+"&#&#,`paybyid5`=&#&#"+payernewid5+"&#&#,`paybyname5`=&#&#"+payernewname5+"&#&#,";

                sqlcmd+="`day6`="+joinday6+",`meal6`="+meal6+",`specialcase6`="+mealx6+",`attend6`="+live6+",`traff6`=&#&#"+traffitem6+"&#&#,`cost6`="+fee6+",`lock6`="+lock+",`pay6`="+paid6+",`regdate6`=&#&#"+regnewdate6+"&#&#,";
                sqlcmd+="`paydate6`=&#&#"+paynewdate6+"&#&#,`paybyid6`=&#&#"+payernewid6+"&#&#,`paybyname6`=&#&#"+payernewname6+"&#&#,";

                sqlcmd+="`memo1`=&#&#"+txtMemo+"&#&#";
                if(cancel1>0){sqlcmd+=(",`cancel1`="+cancel1+",`cancelinfo1`=&#&#"+cancelinfo1+"&#&#");}else if(ocancel1>0&&paid1>0){sqlcmd+=(",`cancel1`=0");}//取消了又繳費
                if(cancel2>0){sqlcmd+=(",`cancel2`="+cancel2+",`cancelinfo2`=&#&#"+cancelinfo2+"&#&#");}else if(ocancel2>0&&paid2>0){sqlcmd+=(",`cancel2`=0");}//取消了又繳費
                if(cancel3>0){sqlcmd+=(",`cancel3`="+cancel3+",`cancelinfo3`=&#&#"+cancelinfo3+"&#&#");}else if(ocancel3>0&&paid3>0){sqlcmd+=(",`cancel3`=0");}//取消了又繳費
                if(cancel4>0){sqlcmd+=(",`cancel4`="+cancel4+",`cancelinfo4`=&#&#"+cancelinfo4+"&#&#");}else if(ocancel4>0&&paid4>0){sqlcmd+=(",`cancel4`=0");}//取消了又繳費
                if(cancel5>0){sqlcmd+=(",`cancel5`="+cancel5+",`cancelinfo5`=&#&#"+cancelinfo5+"&#&#");}else if(ocancel5>0&&paid5>0){sqlcmd+=(",`cancel5`=0");}//取消了又繳費
                if(cancel6>0){sqlcmd+=(",`cancel6`="+cancel6+",`cancelinfo6`=&#&#"+cancelinfo6+"&#&#");}else if(ocancel6>0&&paid6>0){sqlcmd+=(",`cancel6`=0");}//取消了又繳費

                sqlcmd+=clssqlcmd;
                sqlcmd+=" WHERE `idx`="+idx;
            }else{//幹部一般報名
                sqlcmd="UPDATE `"+tbname+"` "+"SET ";
                sqlcmd+="`day1`="+joinday1+",`meal1`="+meal1+",`specialcase1`="+mealx1+",`attend1`="+live1+",`traff1`=&#&#"+traffitem1+"&#&#,`cost1`="+fee1+",`regdate1`=&#&#"+regnewdate1+"&#&#,";
                sqlcmd+="`day2`="+joinday2+",`meal2`="+meal2+",`specialcase2`="+mealx2+",`attend2`="+live2+",`traff2`=&#&#"+traffitem2+"&#&#,`cost2`="+fee2+",`regdate2`=&#&#"+regnewdate2+"&#&#,";
                sqlcmd+="`day3`="+joinday3+",`meal3`="+meal3+",`specialcase3`="+mealx3+",`attend3`="+live3+",`traff3`=&#&#"+traffitem3+"&#&#,`cost3`="+fee3+",`regdate3`=&#&#"+regnewdate3+"&#&#,";
                sqlcmd+="`day4`="+joinday4+",`meal4`="+meal4+",`specialcase4`="+mealx4+",`attend4`="+live4+",`traff4`=&#&#"+traffitem4+"&#&#,`cost4`="+fee4+",`regdate4`=&#&#"+regnewdate4+"&#&#,";
                sqlcmd+="`day5`="+joinday5+",`meal5`="+meal5+",`specialcase5`="+mealx5+",`attend5`="+live5+",`traff5`=&#&#"+traffitem5+"&#&#,`cost5`="+fee5+",`regdate5`=&#&#"+regnewdate5+"&#&#,";
                sqlcmd+="`day6`="+joinday6+",`meal6`="+meal6+",`specialcase6`="+mealx6+",`attend6`="+live6+",`traff6`=&#&#"+traffitem6+"&#&#,`cost6`="+fee6+",`regdate6`=&#&#"+regnewdate6+"&#&#,";

                sqlcmd+="`memo1`=&#&#"+txtMemo+"&#&#";
                sqlcmd+=clssqlcmd;
                sqlcmd+=" WHERE `idx`="+idx;
            }

            allsqlcmd+=sqlcmd;
            allsqlcmd += ";;;;"; //allsqlcmd+="<br>";//debug
        }
        if (dbg == "YES") { $('#msg').html(allsqlcmd); return; }	 // debug show info
        $.ajax({
            async: false,
            url: "./pages/"+sub+"/register.php",
            cache: false,
            dataType: 'html',
            type:'POST',
            data: { sqlcommand: allsqlcmd, jointotal: jointotal, classid: classid, tbname: tb, sub: sub},
            error: function (data) {
                alert("報名失敗!!!");
            },success: function (data) {
                //alert(data);//$('#msg').html(data);	 // debug show info
                if (data < 0 && data > -100000){alert("報名失敗(錯誤代碼:"+data+")!");}
                else if (data <= -100000) {
                    var available = parseInt(-data) - 100000;
                    if (available > 0) {
                        alert("報名失敗 : 目前只剩 " + available + " 位名額，請重新報名!");
                    } else {
                        alert("報名失敗 : 報名已額滿!");
                    }
                }
                else{alert("報名成功!");getclassMember(clsid,clsname,clsarea,clsregion,clshasleader,clsfullname);}
            }
        });
    });


    //-----------------------------------------------------------------------------------------------------------------------------------
    // 匯出報名表
    //-----------------------------------------------------------------------------------------------------------------------------------
    $('#prttable').click(function(){
        var classid=$('#classid').val(); //classid=$(this).val();
        var classname=$('#classid :selected').text();
        var region=$('#classid :selected').attr('regioncode');
        var area=$('#classid :selected').attr('AREAID');//alert(area);return;
        var tbname=$("#tb").val();
        var sub=$('.sub').val();
        if(classname==""||tbname==""){alert("未取得班級資料!");return;}
        //alert(classname);
        $('<form action="./pages/'+sub+'/register-list.php" method="post"><input type="hidden" name="classname" value='+classname+' /><input type="hidden" name="classid" value='+classid+' /><input type="hidden" name="tbname" value='+tbname+' /></form>').appendTo('body').submit().remove();
    });

    //-----------------------------------------------------------------------------------------------------------------------------------
    // 匯出車資繳費單
    //-----------------------------------------------------------------------------------------------------------------------------------
    $('#prtinvoice').click(function(){
        var classid=$('#classid').val(); //classid=$(this).val();
        var classname=$('#classid :selected').text();
        var region=$('#classid :selected').attr('regioncode');
        var area=$('#classid :selected').attr('AREAID');//alert(area);return;
        var tbname=$("#tb").val();
        var trafftb=$("#trafftb").val();
        var sub=$('.sub').val();
        var pujatitle=$('.pujatitle').val();
        var day1title=$('#day1title').val(),day2title=$('#day2title').val(),day3title=$('#day3title').val();
        var day4title=$('#day4title').val(),day5title=$('#day5title').val(),day6title=$('#day6title').val();
        //alert(trafftb+","+tbname+","+classname);
        //return;
        if(classname==""||tbname==""){alert("未取得班級資料!");return;}
        //alert(classname);
        var parameter='<input type="hidden" name="classname" value="'+classname+'" /><input type="hidden" name="classid" value="'+classid+'" />';
        parameter+='<input type="hidden" name="tbname" value="'+tbname+'" /><input type="hidden" name="trafftb" value="'+trafftb+'" />';
        parameter+='<input type="hidden" name="pujaid" value="'+sub+'" />';

        parameter+='<input type="hidden" name="day1title" value="'+day1title+'" /><input type="hidden" name="day2title" value="'+day2title+'" />';
        parameter+='<input type="hidden" name="day3title" value="'+day3title+'" /><input type="hidden" name="day4title" value="'+day4title+'" />';
        parameter+='<input type="hidden" name="day5title" value="'+day5title+'" /><input type="hidden" name="day6title" value="'+day6title+'" />';

        parameter+='<input type="hidden" name="pujatitle" value="'+pujatitle+'" />"';
        $('<form action="./pages/'+sub+'/register-list-invoice.php" method="post">'+parameter+'</form>').appendTo('body').submit().remove();
    });

    //-----------------------------------------------------------------------------------------------------------------------------------
    // 匯出已繳費名冊
    //-----------------------------------------------------------------------------------------------------------------------------------
    $('#paylist').click(function(){
        var classid=$('#classid').val(); //classid=$(this).val();
        var classname=$('#classid :selected').text();
        var region=$('#classid :selected').attr('regioncode');
        var area=$('#classid :selected').attr('AREAID');//alert(area);return;
        var tbname=$("#tb").val();
        var trafftb=$("#trafftb").val();
        var sub=$('.sub').val();
        var pujatitle=$('.pujatitle').val();
        var day1title=$('#day1title').val(),day2title=$('#day2title').val(),day3title=$('#day3title').val();
        var day4title=$('#day4title').val(),day5title=$('#day5title').val(),day6title=$('#day6title').val();
        if(classname==""||tbname==""){alert("未取得班級資料!");return;}
        //alert(classname);
        var parameter='<input type="hidden" name="classname" value="'+classname+'" /><input type="hidden" name="classid" value="'+classid+'" />';
        parameter+='<input type="hidden" name="tbname" value="'+tbname+'" /><input type="hidden" name="trafftb" value="'+trafftb+'" />';
        parameter+='<input type="hidden" name="pujaid" value="'+sub+'" />';

        parameter+='<input type="hidden" name="day1title" value="'+day1title+'" /><input type="hidden" name="day2title" value="'+day2title+'" />';
        parameter+='<input type="hidden" name="day3title" value="'+day3title+'" /><input type="hidden" name="day4title" value="'+day4title+'" />';
        parameter+='<input type="hidden" name="day5title" value="'+day5title+'" /><input type="hidden" name="day6title" value="'+day6title+'" />';

        parameter+='<input type="hidden" name="pujatitle" value="'+pujatitle+'" />"';
        $('<form action="./pages/'+sub+'/pay-list.php" method="post">'+parameter+'</form>').appendTo('body').submit().remove();
    });
    //-----------------------------------------------------------------------------------------------------------------------------------
    // 批次匯出報名表 - 依區域
    //-----------------------------------------------------------------------------------------------------------------------------------
    $('#exportlistarea').click(function(){
        var classarea=$('#classarea').val();//$('#classname').val();
        var tbname=$("#tb").val();
        var sub=$('.sub').val();

        if(classarea==""||tbname==""){
            alert("未指定區域!");
            return;
        }

        //alert(classname);
        $('<form action="./pages/'+sub+'/register-list-all.php" method="post"><input type="hidden" name="classarea" value='+classarea+' /><input type="hidden" name="tbname" value='+tbname+' /></form>').appendTo('body').submit().remove();
    });

    //-----------------------------------------------------------------------------------------------------------------------------------
    // 匯出班級報名統計表
    //-----------------------------------------------------------------------------------------------------------------------------------
    $('#exportclasslist').click(function(){
        var tbname    =$("#tb").val();
        var tbtraffic =$("#traffictb").val();
        var tbstatistic =$("#tbstatistic").val();
        var sub=$('.sub').val();
        if(tbname==""||tbtraffic==""){
            alert("未取得報名統計資料!");
            return;
        }
        //alert(tbtraffic+"   ,    "+tbname+"   ,    "+tbstatistic);
        $('<form action="./pages/'+sub+'/register-list-export-class.php" method="post"><input type="hidden" name="tbname" value='+tbname+' /><input type="hidden" name="tbstatistic" value='+tbstatistic+' /><input type="hidden" name="tbtraffic" value='+tbtraffic+' /></form>').appendTo('body').submit().remove();
    });

    //-----------------------------------------------------------------------------------------------------------------------------------
    // 匯出報名統計表
    //-----------------------------------------------------------------------------------------------------------------------------------
    $('#export').click(function(){
        var tbname=$("#tb").val();
        var tbtraffic=$("#trafftb").val();
        var sub=$('.sub').val();
        var regioncode=$('.regioncode').val();
        var detailinfo=$('#detailinfo').val();
        var leaderinfo=$('#leaderinfo').val();
        var volunteerinfo=$('#volunteerinfo').val();
        var pujatitle=$('#pujatitle').val();
        var day1title=$('#day1title').val(),day2title=$('#day2title').val(),day3title=$('#day3title').val();
        var day4title=$('#day4title').val(),day5title=$('#day5title').val(),day6title=$('#day6title').val();
        if(tbname==""||tbtraffic==""){alert("未取得報名統計資料!");return;}

        var parameter='<input type="hidden" name="tbname" value="'+tbname+'" /><input type="hidden" name="regioncode" value="'+regioncode+'" />';
        parameter+='<input type="hidden" name="tbtraffic" value="'+tbtraffic+'" /><input type="hidden" name="detailinfo" value="'+detailinfo+'" />';
        parameter+='<input type="hidden" name="leaderinfo" value="'+leaderinfo+'" /><input type="hidden" name="volunteerinfo" value="'+volunteerinfo+'" />';
        parameter+='<input type="hidden" name="pujatitle" value="'+pujatitle+'" /><input type="hidden" name="orderbydate" value="NO" />';

        parameter+='<input type="hidden" name="day1title" value="'+day1title+'" /><input type="hidden" name="day2title" value="'+day2title+'" />';
        parameter+='<input type="hidden" name="day3title" value="'+day3title+'" /><input type="hidden" name="day4title" value="'+day4title+'" />';
        parameter+='<input type="hidden" name="day5title" value="'+day5title+'" /><input type="hidden" name="day6title" value="'+day6title+'" />';

        parameter+='<input type="hidden" name="cancelitem" value="NO" />';
        parameter+='"';
        $('<form action="./pages/'+sub+'/export-all.php" method="post">'+parameter+'</form>').appendTo('body').submit().remove();
    });

    //-----------------------------------------------------------------------------------------------------------------------------------
    // 匯出班級報名統計表
    //-----------------------------------------------------------------------------------------------------------------------------------
    $('#classexport').click(function(){
        var tbname=$("#tb").val();
        var tbtraffic=$("#trafftb").val();
        var sub=$('.sub').val();
        var regioncode=$('.regioncode').val();
        var detailinfo=$('#detailinfo').val();
        var leaderinfo=$('#leaderinfo').val();
        var volunteerinfo=$('#volunteerinfo').val();
        var pujatitle=$('#pujatitle').val();

        if(tbname==""||tbtraffic==""){alert("未取得報名統計資料!");return;}

        var parameter='<input type="hidden" name="tbname" value="'+tbname+'" /><input type="hidden" name="regioncode" value="'+regioncode+'" />';
        parameter+='<input type="hidden" name="tbtraffic" value="'+tbtraffic+'" /><input type="hidden" name="detailinfo" value="'+detailinfo+'" />';
        parameter+='<input type="hidden" name="leaderinfo" value="'+leaderinfo+'" /><input type="hidden" name="volunteerinfo" value="'+volunteerinfo+'" />';
        parameter+='<input type="hidden" name="pujatitle" value="'+pujatitle+'" /><input type="hidden" name="orderbydate" value="NO" />';
        parameter+='<input type="hidden" name="cancelitem" value="NO" />';
        parameter+='"';
        $('<form action="./pages/'+sub+'/export-class.php" method="post">'+parameter+'</form>').appendTo('body').submit().remove();
    });
    //-----------------------------------------------------------------------------------------------------------------------------------
    // 匯出學員取消報名統計表
    //-----------------------------------------------------------------------------------------------------------------------------------
    $('#exportcancel').click(function(){
        var tbname=$("#tb").val();
        var tbtraffic=$("#trafftb").val();
        var sub=$('.sub').val();
        var regioncode=$('.regioncode').val();
        var detailinfo=$('#detailinfo').val();
        var leaderinfo=$('#leaderinfo').val();
        var volunteerinfo=$('#volunteerinfo').val();
        var pujatitle=$('#pujatitle').val();
        if(tbname==""||tbtraffic==""){alert("未取得報名統計資料!");return;}

        var parameter='<input type="hidden" name="tbname" value="'+tbname+'" /><input type="hidden" name="regioncode" value="'+regioncode+'" />';
        parameter+='<input type="hidden" name="tbtraffic" value="'+tbtraffic+'" /><input type="hidden" name="detailinfo" value="'+detailinfo+'" />';
        parameter+='<input type="hidden" name="leaderinfo" value="'+leaderinfo+'" /><input type="hidden" name="volunteerinfo" value="'+volunteerinfo+'" />';
        parameter+='<input type="hidden" name="pujatitle" value="'+pujatitle+'" /><input type="hidden" name="orderbydate" value="NO" />';
        parameter+='<input type="hidden" name="cancelitem" value="YES" />';
        parameter+='"';
        $('<form action="./pages/'+sub+'/export-remove.php" method="post">'+parameter+'</form>').appendTo('body').submit().remove();
    });
    //$('#fixheadertbl').fixedHeaderTable('show', 500);
    $('#fixheadertbl').fixedHeaderTable({ footer: true, cloneHeadToFoot: false, altClass: 'odd', autoShow: true });
});

function getclassMember(clsid,clsname,clsarea,clsregion,clshasleader,clsfullname){
    $('#queryresult').html("");
    var classid=clsid; //classid=$(this).val();
    var classname=clsname;
    var region=clsregion;
    var area=clsarea;//alert(area);return;
    var hasleader=clshasleader;
    var classfullname=clsfullname;
    if(classid==""||classname==""||area==""||region==""||classfullname==""){
        classid=$('#classid').val(); //classid=$(this).val();
        classname=$('#classid :selected').text();
        region=$('#classid :selected').attr('regioncode');
        area=$('#classid :selected').attr('AREAID');//alert(area);return;
        classfullname=$('#classid :selected').attr('classname');//alert(area);return;
        hasleader=$('#Major').val();
    }

    var sub=$('.sub').val();
    var tb=$('.tb').val();
    var trafftb=$('.trafftb').val();
    var Major=$('.Major').val();
    var detailinfo=$('#detailinfo').val();
    var leaderinfo=$('#leaderinfo').val();
    var volunteerinfo=$('#volunteerinfo').val();
    var user = $('#currentacc').val();

    if(classname=='-'){alert("尚未指定班級!");return;}
    // 送出查詢關鍵字至後端
    $.ajax({
        async: false,
        url: "./pages/"+sub+"/queryquery.php",
        cache: false,
        dataType: 'html',
        type:'POST',
        data:{classname:classname, classid:classid, tbname:tb, trafftbname:trafftb,Major:Major,user: user,detailinfo:detailinfo,leaderinfo:leaderinfo,volunteerinfo:volunteerinfo,area:area,region:region,classfullname:classfullname},
        error: function (data) {
            alert("查詢班級資料失敗!!!");//$('#queryresult').html(data);
        },success: function (data) {
            if(data==0){
                $('#queryresult').html("查無資料!");
            } else if (data == -1 || (data == "-1")) {
                $('#queryresult').html('<span class="red" style="color:#F00;font-size:14pt;">抱歉! 報名人數已額滿!</span>');
            } else {
                showtable(data,classid,classname,area,region,classfullname);
            }//$('#queryresult').html(data);
        }
    });
}

function showtable(data,classid,classname,area,region,classfullname){
    //$('#queryresult').html(data);return; //debug
    var partsArray=data.split(';');
    var showdata="";
    if(partsArray.length<=0){return;}

    var payitem=$('#payitem').val(); //classid=$(this).val();
    var trafftable=partsArray[0].split('-');
    var trafftable1=trafftable[0].split('|');
    if(trafftable.length>=2){var trafftable2=trafftable[1].split('|');}
    if(trafftable.length>=3){var trafftable3=trafftable[2].split('|');}
    if(trafftable.length>=4){var trafftable4=trafftable[3].split('|');}
    if(trafftable.length>=5){var trafftable5=trafftable[4].split('|');}
    if(trafftable.length>=6){var trafftable6=trafftable[5].split('|');}

    var fami=[];
    for(x=0;x<11;x++){fami.push(x);}
    var day1title=$('#day1title').val(),day2title=$('#day2title').val(),day3title=$('#day3title').val();
    var day4title=$('#day4title').val(),day5title=$('#day5title').val(),day6title=$('#day6title').val();
    // Head ROW1
    var table1="<table class=\"reference\" id=\"fixheadertbl1\" style=\"width:800px\" align=\"center\">";
    table1+="<thead><tr>";
    table1+="<th></th><th></th>";//序 姓名
    table1+="<th></th>";// X
    table1+="<th colspan='2' style='width:240px;'>"+day1title+"</th>";
    //table1+="<th colspan='2' style='width:150px;'>"+day2title+"</th>";
    //table1+="<th colspan='2' style='width:150px;'>"+day3title+"</th>";
    //table1+="<th colspan='2' style='width:150px;'>"+day4title+"</th>";
    //table1+="<th colspan='2' style='width:150px;'>"+day5title+"</th>";
    //table1+="<th colspan='2' style='width:150px;'>"+day6title+"</th>";

    //table1+="<th></th>";// 姓名 - hint

    table1+="<th></th>";//車資
    table1+="<th></th>";//備註
    if(payitem=="YES"){table1+="<th></th>";}
    //table1+="<th></th>";//預留
    // HEAD ROW2
    table1+="</tr><tr>";
    table1+="<th style=\"width:25px;\">序</th>";
    table1+="<th style=\"width:65px;\">姓名</th>";
    table1+="<th style=\"width:30px;\">X</th>";

    table1+="<th style=\"width:60px;\">參加</th><th style=\"width:240px;\">交通</th>";
    //table1+="<th style=\"width:30px;\">參加</th><th style=\"width:120px;\">交通</th>";
    //table1+="<th style=\"width:30px;\">參加</th><th style=\"width:120px;\">交通</th>";
    //table1+="<th style=\"width:30px;\">參加</th><th style=\"width:120px;\">交通</th>";
    //table1+="<th style=\"width:30px;\">參加</th><th style=\"width:30px;\">午餐</th><th style=\"width:30px;\">住宿</th><th style=\"width:120px;\">交通</th>";
    //table1+="<th style=\"width:30px;\">參加</th><th style=\"width:30px;\">午餐</th><th style=\"width:120px;\">交通</th>";

    //table1+="<th style=\"width:60px;\">姓名</th>";

    table1+="<th style=\"width:50px;\">車資</th><th style=\"width:160px;\">備註</th>";
    if(payitem=="YES"){table1+="<th style=\"width:30px;\">繳費</th>";}
    //table1+="<th></th>";//預留
    table1+="</thead><tbody><tr></tr>";

    var istep=0;
    var idx=0;
    var lock=0;
    chkJoin=" checked ";chkMeal=" checked ";chkLive=" checked ";traff="Z";tfround=" "; chkPay=" "; selected=" ";
    selected0=" ";selected1=" ";selected2=" ";

    for(w=1;w<partsArray.length;w++){
        if(partsArray[w]==""){break;}
        var row=partsArray[w].split('|');
        if(row.length<5){continue;}

        lock=0;
        if(payitem=="YES"){lock=0;}else{lock=row[11];}//管理者可勾選繳費
        disabledlock="  ";if(lock>=1){disabledlock=" disabled ";}

        day1=parseInt(row[12]);day2=parseInt(row[32]);day3=parseInt(row[52]);day4=parseInt(row[72]);day5=parseInt(row[92]);day6=parseInt(row[112]);
        day=day1+(day2*10)+(day3*100)+(day4*1000)+(day5*10000)+(day6*100000);
        idx=parseInt(row[0]);
        meal1=parseInt(row[13]);meal2=parseInt(row[33]); meal3=parseInt(row[53]);meal4=parseInt(row[73]);meal5=parseInt(row[93]);meal6=parseInt(row[113]);
        mealx1=parseInt(row[17]);mealx2=parseInt(row[37]); mealx3=parseInt(row[57]);mealx4=parseInt(row[77]);mealx5=parseInt(row[97]);mealx6=parseInt(row[117]);
        live1=parseInt(row[21]);live2=parseInt(row[41]); live3=parseInt(row[61]);live4=parseInt(row[81]);live5=parseInt(row[101]);live6=parseInt(row[121]);
        cost1=parseInt(row[19]);cost2=parseInt(row[39]);cost3=parseInt(row[59]);cost4=parseInt(row[79]);cost5=parseInt(row[99]);cost6=parseInt(row[119]);
        cost=cost1+cost2+cost3+cost4+cost5+cost6;

        var daytraff1=row[18].split(',');traff1=daytraff1[0];if(traff1==""||typeof(traff1)=="undefined"){traff1="Z";}traffround1=daytraff1[1];if(traffround1==""||typeof(traffround1)=="undefined"){traffround1="0";}
        var daytraff2=row[38].split(',');traff2=daytraff2[0];if(traff2==""||typeof(traff2)=="undefined"){traff2="Z";}traffround2=daytraff2[1];if(traffround2==""||typeof(traffround2)=="undefined"){traffround2="0";}
        var daytraff3=row[58].split(',');traff3=daytraff3[0];if(traff3==""||typeof(traff3)=="undefined"){traff3="Z";}traffround3=daytraff3[1];if(traffround3==""||typeof(traffround3)=="undefined"){traffround3="0";}
        var daytraff4=row[78].split(',');traff4=daytraff4[0];if(traff4==""||typeof(traff4)=="undefined"){traff4="Z";}traffround4=daytraff4[1];if(traffround4==""||typeof(traffround4)=="undefined"){traffround4="0";}
        var daytraff5=row[98].split(',');traff5=daytraff5[0];if(traff5==""||typeof(traff5)=="undefined"){traff5="Z";}traffround5=daytraff5[1];if(traffround5==""||typeof(traffround5)=="undefined"){traffround5="0";}
        var daytraff6=row[118].split(',');traff6=daytraff6[0];if(traff6==""||typeof(traff6)=="undefined"){traff6="Z";}traffround6=daytraff6[1];if(traffround6==""||typeof(traffround6)=="undefined"){traffround6="0";}

        // keep the old data
        table1+="<tr><td id='idx_"+(w)+"'' class='idx' idx='"+idx+"' serial='"+(w)+"' lock='"+lock+"'";
        table1+=" regdate1='"+row[22]+"' regdate2='"+row[42]+"'  regdate3='"+row[62]+"'  regdate4='"+row[82]+"'  regdate5='"+row[102]+"'  regdate6='"+row[122]+"'";
        table1+=" paydate1='"+row[23]+"' payerid1='"+row[25]+"' payername1='"+row[26]+"' ojoinday1='"+row[12]+"'";
        table1+=" paydate2='"+row[43]+"' payerid2='"+row[45]+"' payername2='"+row[46]+"' ojoinday2='"+row[32]+"'";
        table1+=" paydate3='"+row[63]+"' payerid3='"+row[65]+"' payername3='"+row[66]+"' ojoinday3='"+row[52]+"'";
        table1+=" paydate4='"+row[83]+"' payerid4='"+row[85]+"' payername4='"+row[86]+"' ojoinday4='"+row[72]+"'";
        table1+=" paydate5='"+row[103]+"' payerid5='"+row[105]+"' payername5='"+row[106]+"' ojoinday5='"+row[92]+"'";
        table1+=" paydate6='"+row[123]+"' payerid6='"+row[125]+"' payername6='"+row[126]+"' ojoinday6='"+row[112]+"'";

        table1+=" traffic1='"+traff1+" trafficround1='"+traffround1+"' fee1='"+row[19]+"' cancel1='"+row[27]+"' cancelinfo1='"+row[28]+"'";
        table1+=" traffic2='"+traff2+" trafficround2='"+traffround2+"' fee2='"+row[39]+"' cancel2='"+row[47]+"' cancelinfo2='"+row[48]+"'";
        table1+=" traffic3='"+traff3+" trafficround3='"+traffround3+"' fee3='"+row[59]+"' cancel3='"+row[67]+"' cancelinfo3='"+row[68]+"'";
        table1+=" traffic4='"+traff4+" trafficround4='"+traffround4+"' fee4='"+row[79]+"' cancel5='"+row[87]+"' cancelinfo4='"+row[88]+"'";
        table1+=" traffic5='"+traff5+" trafficround5='"+traffround5+"' fee5='"+row[99]+"' cancel6='"+row[107]+"' cancelinfo5='"+row[108]+"'";
        table1+=" traffic6='"+traff6+" trafficround6='"+traffround6+"' fee6='"+row[119]+"' cancel7='"+row[127]+"' cancelinfo6='"+row[128]+"'";

        table1+=" style='text-align:center;'>"+(w)+"</td>";//序-記錄相關informaion

        table1+="<td style='text-align:center;' id='student_"+(w)+"'' class='student' idx='"+row[1]+"' serial='"+(w)+"'>"+row[1]+"</td>";//姓名
        //table1+="<td style='text-align:center;'>"+row[2]+"</td>";//身份

        disableditem="  ";if(day>0){disableditem=" disabled ";}

        // 不參加
        chkNotJoin=" checked ";chkJoin="  ";disableTraff=" disabled ";
        if(day>0){chkNotJoin="  ";chkJoin=" checked ";disableTraff=" ";}
        table1+="<td style='text-align:center;'><input id='notjoin_"+(w)+"' serial='"+(w)+"' idx='"+idx+"' class='notjoin' type='radio'"+chkNotJoin+disabledlock+"></td>";

        //場1學員&餐&交通(去回)
        chkJoin="  ";disableditem=" disabled ";if(day1>0){chkJoin=" checked ";disableditem=" ";}else{chkJoin=" ";}
        table1+="<td style='text-align:center;'><input id='join1_"+(w)+"' serial='"+(w)+"' Item=1 idx='"+idx+"' class='join' type='checkbox'"+chkJoin+disabledlock+"></td>";

        //chkJoin="  ";disableditem=" disabled ";if(day1>0){disableditem=" ";}if(meal1>0){chkJoin=" checked ";disableditem=" ";}else{chkJoin=" ";}
        //table1+="<td style='text-align:center;'><input id='meal1_"+(w)+"' serial='"+(w)+"' Item=1 idx='"+idx+"' class='meal' type='checkbox'"+chkJoin+disableditem+disabledlock+"></td>";

        // 住宿 & 早餐
        //chkJoin="  ";disableditem=" disabled ";if(day1>0){disableditem=" ";}if(mealx1>0){chkJoin=" checked ";disableditem=" ";}else{chkJoin=" ";}
        //table1+="<td style='text-align:center;'><input id='live1_"+(w)+"' serial='"+(w)+"' Item=1 idx='"+idx+"' class='live' type='checkbox'"+chkJoin+disableditem+disabledlock+"></td>";
        //chkJoin="  ";disableditem=" disabled ";if(day1>0){disableditem=" ";}if(live1>0){chkJoin=" checked ";disableditem=" ";}else{chkJoin=" ";}
        //table1+="<td style='text-align:center;'><input id='mealx1_"+(w)+"' serial='"+(w)+"' Item=1 idx='"+idx+"' class='meal' type='checkbox'"+chkJoin+disableditem+disabledlock+"></td>";

        chkJoin="  ";disableditem=" disabled ";if(day1>0){chkJoin=" checked ";disableditem=" ";}else{chkJoin=" ";}
        table1+="<td style='text-align:center;'><select style='width:180px;' id='traffic1_"+(w)+"' class='traffic' serial='"+(w)+"' Item=1 idx='"+idx+"' name='traffic"+w+"' "+disableditem+disabledlock+">";
        for(kk=0;kk<trafftable1.length;kk+=2){selected=(traff1==trafftable1[kk] ? "selected":" ");table1+="<option value='"+trafftable1[kk]+"' "+selected+">"+trafftable1[kk]+"-"+trafftable1[kk+1]+"</option>";}//{table1 +="<option value='' "+trafftable[kk]+"-"+trafftable[kk+1]+"></option>";}
        table1 +="</select>&nbsp;";

        //select0="";if(traffround1==0){select0=" selected ";}select1="";if(traffround1==1){select1=" selected ";}select2="";if(traffround1==2){select2=" selected ";}
        //table1+="<select style='width:55px' id='trafficround1_"+(w)+"' class='trafficround' serial='"+(w)+"' Item=1 idx='"+idx+"' name='trafficround"+w+"' "+disableditem+disabledlock+">";
        //table1+="<option value='0' "+select0+">去回</option><option value='1' "+select1+">去</option><option value='2' "+select2+">回</option>";
        //table1 +="</select></td>";

        //場2學員&餐&交通(去回)
        //chkJoin="  ";disableditem=" disabled ";if(day2>0){chkJoin=" checked ";disableditem=" ";}else{chkJoin=" ";}
        //table1+="<td style='text-align:center;'><input id='join2_"+(w)+"' serial='"+(w)+"' Item=2 idx='"+idx+"' class='join' type='checkbox'"+chkJoin+disabledlock+"></td>";

        //chkJoin="  ";disableditem=" disabled ";if(day2>0){disableditem=" ";}if(meal2>0){chkJoin=" checked ";disableditem=" ";}else{chkJoin=" ";}
        //table1+="<td style='text-align:center;'><input id='meal2_"+(w)+"' serial='"+(w)+"' Item=2 idx='"+idx+"' class='meal' type='checkbox'"+chkJoin+disableditem+disabledlock+"></td>";

        // 住宿 & 早餐
        //chkJoin="  ";disableditem=" disabled ";if(day2>0){disableditem=" ";}if(mealx2>0){chkJoin=" checked ";disableditem=" ";}else{chkJoin=" ";}
        //table1+="<td style='text-align:center;'><input id='live2_"+(w)+"' serial='"+(w)+"' Item=2 idx='"+idx+"' class='live' type='checkbox'"+chkJoin+disableditem+disabledlock+"></td>";
        //chkJoin="  ";disableditem=" disabled ";if(day2>0){disableditem=" ";}if(live2>0){chkJoin=" checked ";disableditem=" ";}else{chkJoin=" ";}
        //table1+="<td style='text-align:center;'><input id='mealx2_"+(w)+"' serial='"+(w)+"' Item=2 idx='"+idx+"' class='meal' type='checkbox'"+chkJoin+disableditem+disabledlock+"></td>";

        //chkJoin="  ";disableditem=" disabled ";if(day2>0){chkJoin=" checked ";disableditem=" ";}else{chkJoin=" ";}
        //table1+="<td style='text-align:center;'><select style='width:45px;' id='traffic2_"+(w)+"' class='traffic' serial='"+(w)+"' Item=2 idx='"+idx+"' name='traffic"+w+"' "+disableditem+disabledlock+">";
        //for(kk=0;kk<trafftable2.length;kk+=2){selected=(traff2==trafftable2[kk] ? "selected":" ");table1+="<option value='"+trafftable2[kk]+"' "+selected+">"+trafftable2[kk]+"-"+trafftable2[kk+1]+"</option>";}//{table1 +="<option value='' "+trafftable[kk]+"-"+trafftable[kk+1]+"></option>";}
        //table1 +="</select>&nbsp;";

        //select0="";if(traffround2==0){select0=" selected ";}select1="";if(traffround2==1){select1=" selected ";}select2="";if(traffround2==2){select2=" selected ";}
        //table1+="<select style='width:55px' id='trafficround2_"+(w)+"' class='trafficround' serial='"+(w)+"' Item=2 idx='"+idx+"' name='trafficround"+w+"' "+disableditem+disabledlock+">";
        //table1+="<option value='0' "+select0+">去回</option><option value='1' "+select1+">去</option><option value='2' "+select2+">回</option>";

        //table1 +="</select></td>";

        // hint of name
        //table1+="<td style='text-align:center;'>"+row[1]+"</td>";//姓名

        //車資
        table1+="<td style='text-align:center;'><div style='width:45px;' id='fee_"+(w)+"' class='fee' serial='"+(w)+"' idx='"+idx+"' name='fee"+w+"'>"+cost+"<div></td>";

        //備註
        //var memo=row[29].replace("\'", "&#039;");
        table1+="<td style='text-align:center;'><input style='width:150px' id='memo_"+(w)+"' serial='"+(w)+"' idx='"+idx+"' class='memo' type='text'"+" value='"+row[29]+"'></td>";

        //繳費
        if(payitem=="YES")
        {
            chkPay="  ";if(row[20]>0||row[40]>0||row[60]>0||row[80]>0||row[100]>0||row[120]>0){chkPay=" checked ";}
            disableditem="  ";if(day<=0||cost<=0){disableditem=" disabled ";}//不參加或無須繳費 - 無須繳費
            table1+="<td style='text-align:center;'><input id='pay_"+(w)+"' serial='"+(w)+"' idx='"+idx+"' class='pay' type='checkbox'"+chkPay+disableditem+">";
        }

        //table1+="<td></td>";//預留
        table1+="</tr>";
    }

    table1+="</table>";
    showdata+="<div id=\"tabs-1\" class=\"grid_x height450\" style=\"width:800px;\">"+table1+"</div>";
    showdata+="<input type='hidden' id='memberCnt' class='memberCnt' name='memberCnt' value='"+(partsArray.length-2)+"' />";

    showdata+="<input type='hidden' id='clsid' class='clsid' name='clsid' value='"+(classid)+"' />";
    showdata+="<input type='hidden' id='clsname' class='clsname' name='clsname' value='"+(classname)+"' />";
    showdata+="<input type='hidden' id='clsarea' class='clsarea' name='clsarea' value='"+(area)+"' />";
    showdata+="<input type='hidden' id='clsregion' class='clsregion' name='clsregion' value='"+(region)+"' />";
    showdata+="<input type='hidden' id='clsfullname' class='clsfullname' name='clsfullname' value='"+(classfullname)+"' />";

    //showtable(showdata);
    $('#queryresult').html(showdata);
    $('#fixheadertbl1').fixedHeaderTable({ footer: true, cloneHeadToFoot: false, altClass: 'odd', autoShow: true});

    $('.notjoin').click(function(event){
        var serial=$(this).attr('serial');
        for(wx=1;wx<=6;wx++){
            $('#join'+wx+'_'+serial).prop('checked',false);
            $('#traffic'+wx+'_'+serial+' option[value="Z"]').attr('selected', 'selected');
            $('#traffic'+wx+'_'+serial).val("Z").change();
            $('#traffic'+wx+'_'+serial).prop('disabled',true);
            $('#trafficround'+wx+'_'+serial+' option[value="0"]').attr('selected', 'selected');
            $('#trafficround'+wx+'_'+serial).val("0").change();
            $('#trafficround'+wx+'_'+serial).prop('disabled',true);
            $('#meal'+wx+'_'+serial).prop('checked',false);
            $('#meal'+wx+'_'+serial).prop('disabled',true);
            $('#mealx'+wx+'_'+serial).prop('checked',false);
            $('#mealx'+wx+'_'+serial).prop('disabled',true);
            $('#live'+wx+'_'+serial).prop('checked',false);
            $('#live'+wx+'_'+serial).prop('disabled',true);
        }
        $('#fee_'+serial).text("0");
        $('#pay_'+serial).prop('checked',false);
        $('#pay_'+serial).prop('disabled',true);
    });

    $('.join').click(function(event){
        var serial=$(this).attr('serial');
        var item=$(this).attr('Item');

        var day1=$("#join1_"+serial).is(':checked') ? 1 : 0;
        var day2=$("#join2_"+serial).is(':checked') ? 1 : 0;
        var day3=$("#join3_"+serial).is(':checked') ? 1 : 0;
        var day4=$("#join4_"+serial).is(':checked') ? 1 : 0;
        var day5=$("#join5_"+serial).is(':checked') ? 1 : 0;
        var day6=$("#join6_"+serial).is(':checked') ? 1 : 0;

        if((day1+day2+day3+day4+day5+day6)<=0){
            $('#notjoin_'+serial).prop('checked',true);
            for(ww=1;ww<=6;ww++){
                $('#traffic'+ww+'_'+serial+' option[value="Z"]').attr('selected', 'selected');
                $('#traffic'+ww+'_'+serial).val("Z").change();
                $('#traffic'+ww+'_'+serial).prop('disabled',true);
                $('#trafficround'+ww+'_'+serial+' option[value="0"]').attr('selected', 'selected');
                $('#trafficround'+ww+'_'+serial).val("0").change();
                $('#trafficround'+ww+'_'+serial).prop('disabled',true);
                $('#meal'+ww+'_'+serial).prop('checked',false);
                $('#meal'+ww+'_'+serial).prop('disabled',true);
                $('#mealx'+ww+'_'+serial).prop('checked',false);
                $('#mealx'+ww+'_'+serial).prop('disabled',true);
                $('#live'+ww+'_'+serial).prop('checked',false);
                $('#live'+ww+'_'+serial).prop('disabled',true);
            }
            $('#fee_'+serial).text("0");
            $('#pay_'+serial).prop('checked',false);
            $('#pay_'+serial).prop('disabled',true);
        }else{
            $('#notjoin_'+serial).prop('checked',false);
        }
        dayvalue=0;
        if(item==1){dayvalue=day1;}else if(item==2){dayvalue=day2;}else if(item==3){dayvalue=day3;}else if(item==4){dayvalue=day4;}else if(item==5){dayvalue=day5;}else if(item==6){dayvalue=day6;}

        if(dayvalue<=0){
            $('#traffic'+item+'_'+serial+' option[value="Z"]').attr('selected', 'selected');
            $('#traffic'+item+'_'+serial).val("Z").change();
            $('#traffic'+item+'_'+serial).prop('disabled',true);
            $('#trafficround'+item+'_'+serial+' option[value="0"]').attr('selected', 'selected');
            $('#trafficround'+item+'_'+serial).val("0").change();
            $('#trafficround'+item+'_'+serial).prop('disabled',true);
            $('#meal'+item+'_'+serial).prop('checked',false);
            $('#meal'+item+'_'+serial).prop('disabled',true);
            $('#mealx'+item+'_'+serial).prop('checked',false);
            $('#mealx'+item+'_'+serial).prop('disabled',true);
            $('#live'+item+'_'+serial).prop('checked',false);
            $('#live'+item+'_'+serial).prop('disabled',true);
        }else{
            $('#traffic'+item+'_'+serial).prop('disabled',false);
            $('#trafficround'+item+'_'+serial).prop('disabled',false);
            $('#meal'+item+'_'+serial).prop('disabled',false);
            $('#mealx'+item+'_'+serial).prop('disabled',false);
            $('#live'+item+'_'+serial).prop('disabled',false);
        }
        $('#fee_'+serial).text(getfee(serial));
    });

    $('.traffic').on('change', function()
    {
        //var item=$(this).attr('Item');
        var serial=$(this).attr('serial');//alert($('#traffic1_'+serial).val()+"-"+$('#traffic2_'+serial).val()+"-"+getfee(serial));
        $('#fee_'+serial).text(getfee(serial));
    });

    $('.trafficround').on('change', function()
    {
        var serial=$(this).attr('serial');
        $('#fee_'+serial).text(getfee(serial));
    });

    $('.pay').click(function(event)
    {
        var serial=$(this).attr('serial');
        if($(this).is(':checked')){$('#notjoin_'+serial).prop('disabled',true);$('#join_'+serial).prop('disabled',true);$('#traffic_'+serial).prop('disabled',true);}
        else{$('#notjoin_'+serial).prop('disabled',false);$('#join_'+serial).prop('disabled',false);if($('#join_'+serial).is(':checked')){$('#traffic_'+serial).prop('disabled',false);}}
    });
}

function getfee(index){
    var fee=0;
    var traffroundfee=parseInt($('.traffroundfee').val());
    var traffgofee=parseInt($('.traffgofee').val());
    var traffbackfee=parseInt($('.traffbackfee').val());
    var traffoverdayfee=parseInt($('.traffoverdayfee').val());
    //var meal1= ($('#meal1_'+index).is(':checked')) ? 50: 0;
    for(w=1;w<=6;w++){
        if($('#join'+w+'_'+index).is(':checked')){
            if($('#traffic'+w+'_'+index).val()!="Z"&&$('#traffic'+w+'_'+index).val()!="ZZ"){
                if(parseInt($('#trafficround'+w+'_'+index).val())==0){fee+=traffroundfee;}//去回
                else if(parseInt($('#trafficround'+w+'_'+index).val())==1){fee+=traffgofee;}//去
                else if(parseInt($('#trafficround'+w+'_'+index).val())==2){fee+=traffbackfee;}//回
                else {
                    fee += traffroundfee;
                }
            }
        }
    }
    if(fee<=0){$('#pay_'+index).prop('checked',false);$('#pay_'+index).prop('disabled',true);}
    else{$('#pay_'+index).prop('disabled',false);}
    return fee;
}

function getfeebyitem(index,w){
    var fee=0;
    var traffroundfee=parseInt($('.traffroundfee').val());
    var traffgofee=parseInt($('.traffgofee').val());
    var traffbackfee=parseInt($('.traffbackfee').val());
    var traffoverdayfee=parseInt($('.traffoverdayfee').val());

    if($('#join'+w+'_'+index).is(':checked')){
        if($('#traffic'+w+'_'+index).val()!="Z"&&$('#traffic'+w+'_'+index).val()!="ZZ"){
            if(parseInt($('#trafficround'+w+'_'+index).val())==0){fee+=traffroundfee;}
            else if(parseInt($('#trafficround'+w+'_'+index).val())==1){fee+=traffgofee;}
            else if(parseInt($('#trafficround'+w+'_'+index).val())==2){fee+=traffbackfee;}
            else {
                fee += traffroundfee;
            }
        }
    }
    return fee;
}
