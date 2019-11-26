--kidscoreにスーパーユーザー権限をあたえるか、スーパーユーザーで実行
--現行サーバに接続
select dblink_connect('con111','hostaddr=192.168.1.111 port=5432 dbname=kidscore2 user=kids password=kids');

