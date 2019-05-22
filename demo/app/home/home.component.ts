/**
 * @Author: jibon
 * @Date:   2017-09-19T12:29:38+08:00
 * @Last modified by:   jibon
 * @Last modified time: 2017-09-19T12:29:38+08:00
 */

import { Component, OnInit } from "@angular/core";
import { CommonServices } from "../services/common.services";

import * as app from "application";
import dialogs = require("ui/dialogs");
import { TNSFancyAlert, TNSFancyAlertButton } from 'nativescript-fancyalert';

declare var android: any;

@Component({
    selector: "Home",
    moduleId: module.id,
    templateUrl: "./home.component.html"
})
export class HomeComponent implements OnInit {

    public out_trade_no;
    public product = {
        "product_name": "test",
        "product_price": 0.1
    }

    constructor(public common: CommonServices) {
    }

    ngOnInit(): void {
    }

    public startAlipay() {
        if (this.checkPackage("com.eg.android.AlipayGphone")) {
            this.payWithAlipay();
        } else {
            alert("You don't have Alipay app install.");
        }
    }

    public startWeChat() {
        if (this.checkPackage("com.tencent.mm")) {
            this.payWithWechat();
        } else {
            alert("You don't have Wechat app install.");
        }
    }

    public checkPackage(pk) {
        let pm = app.android.context.getPackageManager();
        try {
            let pg = pm.getApplicationInfo(pk, 0);
            if (pg.enabled) {
                return true;
            }
        } catch (err) {
            return false;
        }
        return false;
    }

    public payWithAlipay() {
        this.common.loaderIndicator(true, "Just a sec..")
        this.common.getAlipayUrl(this.product.product_name, this.product.product_price).subscribe(res => {
            this.common.loaderIndicator(false);
            console.dir(res);
            this.out_trade_no = res.out_trade_no;
            this.openAlipay(res.url);
        }, err => {
            this.common.loaderIndicator(false);
            TNSFancyAlert.showError("Error", "Something went wrong!!", "Close");
        })
    }

    public openAlipay(url) {

        let activity = app.android.foregroundActivity;
        let t = this;
        let i = new android.content.Intent(android.content.Intent.ACTION_VIEW);
        i.setData(android.net.Uri.parse(url));

        activity.startActivityForResult(i, 99);
        app.android.on(app.AndroidApplication.activityResultEvent, onResult);

        function onResult (args) {
            app.android.off(app.AndroidApplication.activityResultEvent, onResult);
            //let androidAcivity = android.app.Activity;
            if (args.requestCode == 99) {
                setTimeout(function () {
                    dialogs.alert({
                        title: "Info",
                        message: "Thank you for order.",
                        okButtonText: "OK"
                    }).then(function () {
                        t.verifyAlipay();
                    });
                }, 500)
            }
        }
    }

    public verifyAlipay() {
        let t = this;
        t.common.loaderIndicator(true, "Checking order...");
        t.common.verifyAlipay(t.out_trade_no).subscribe(res => {
            t.common.loaderIndicator(false);
            if (res.code == "10000") {
                TNSFancyAlert.showSuccess("Order Confirmed", "Success", "OK")
            } else {
                TNSFancyAlert.showError("Error", res.msg + " " + res.sub_msg, "Close");
            }

        }, err => {
            t.common.loaderIndicator(false);
            TNSFancyAlert.showError("Error", "Something went wrong!!", "Close");
        })
    }

    public payWithWechat() {
        this.common.loaderIndicator(true, "Just a sec..")
        this.common.getWechatUrl(this.product.product_name, this.product.product_price).subscribe(res => {
            this.common.loaderIndicator(false);
            console.dir(res);
            this.out_trade_no = res.out_trade_no;
            this.openWechat(res.url);
        }, err => {
            this.common.loaderIndicator(false);
            TNSFancyAlert.showError("Error", "Something went wrong!!", "Close");
        })
    }

    public openWechat(url) {
        
        let t = this;
        let activity = app.android.foregroundActivity;

        let i = new android.content.Intent(android.content.Intent.ACTION_VIEW);
        i.setData(android.net.Uri.parse(url));

        activity.startActivityForResult(i, 299);
        app.android.on(app.AndroidApplication.activityResultEvent, onResult);

        function onResult (args) {
            app.android.off(app.AndroidApplication.activityResultEvent, onResult);
            if (args.requestCode == 299) {
                setTimeout(function () {
                    t.verifyWechat();
                }, 100)
            }
        }

    }

    public verifyWechat() {
        let t = this;
        t.common.loaderIndicator(true, "Checking order...");
        t.common.verifyWechat(t.out_trade_no).subscribe(res => {
            t.common.loaderIndicator(false);
            if (res.result_code == "SUCCESS") {
                if (res.trade_state == "SUCCESS") {
                    TNSFancyAlert.showSuccess("Order Confirmed", "Success", "OK")
                } else {
                    TNSFancyAlert.showError("Error", "Order was unsuccessfull " + res.trade_state_desc, "Close");
                }
            } else {
                TNSFancyAlert.showError("Error", "Something went wrong!! " + res.err_code_des, "Close");
            }
        }, err => {
            t.common.loaderIndicator(false);
            TNSFancyAlert.showError("Error", "Something went wrong!!", "Close");
        })
    }
}
