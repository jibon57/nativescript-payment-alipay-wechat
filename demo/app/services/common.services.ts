/**
 * @Author: jibon
 * @Date:   2017-09-19T12:29:38+08:00
 * @Last modified by:   jibon
 * @Last modified time: 2017-09-19T12:29:38+08:00
 */

import { Injectable } from '@angular/core';
import { Http } from "@angular/http";
import 'rxjs/add/operator/map';

let LoadingIndicator = require("nativescript-loading-indicator").LoadingIndicator;

@Injectable()

export class CommonServices {

    private url = "https://example.com/"; // use your domain
    public loader: any;

    constructor(private http: Http) {
        this.loader = new LoadingIndicator();
    }

    public loaderIndicator(show = false, msg = "Loading...") {

        var loaderOpt = {
            message: msg,
            android: {
                cancelable: true,
            },
        };
        
        if (show) {
            this.loader.show(loaderOpt);
        } else {
            this.loader.hide();
        }
    }

    public getWechatUrl(name, price) {
        return this.http.get(this.url + "wechat.php?name=" + name + "&price=" + price)
            .map(res => res.json())
    }

    public verifyWechat(out_trade_no) {
        return this.http.get(this.url + "wechat.php?getQuery=true&out_trade_no=" + out_trade_no)
            .map(res => res.json())
    }

    public getAlipayUrl(name, price) {
        return this.http.get(this.url + "alipay.php?name=" + name + "&price=" + price)
            .map(res => res.json())
    }

    public verifyAlipay(out_trade_no) {
        return this.http.get(this.url + "alipay.php?getQuery=true&out_trade_no=" + out_trade_no)
            .map(res => res.json())
    }

}