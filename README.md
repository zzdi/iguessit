# iguessit
利用微信自动回复做的猜数字小游戏
## config.php
* 配置微信对接的token。
## wechat.php
* 微信通信主要类，来自微信公众平台下载sdk。
## index.php
* 猜数字的主要逻辑处理，因为无法使用session或cookie，又不想使用数据库，用户历史猜测数据直接存储为：用户id.txt，请保证目录可写。
## 猜数字玩法
* 系统设定一个没有重复的4位数字(1-9)，用户猜这个数字，每猜一次系统会提示几A几B，其中A前面的数字表示位置正确的数的个数，而B前的数字表示数字正确而位置不对的数的个数。每局最多猜10次。
* 关注微信公众号：iguessit 可测试线上项目。
* <img src="https://github.com/zzdi/iguessit/blob/master/qrcode.jpg" />
