===================================
市のヒートマップを作ろう
===================================

市を小学校区で区切った単位で可視化してビジュアル的に訴えよう！

完成イメージ
============

==========================================================================================================   ============
データマッピング前                                                                                           完成イメージ
==========================================================================================================   ============
.. image:: https://raw.githubusercontent.com/kon104/tzuka/master/qgis/takarazuka-area-elementary-white.png   .. image:: https://raw.githubusercontent.com/kon104/tzuka/master/qgis/takarazuka-area-elementary-heat.png
==========================================================================================================   ============

- https://github.com/kon104/tzuka/blob/master/qgis/takarazuka-area-elementary-white.png
- https://github.com/kon104/tzuka/blob/master/qgis/takarazuka-area-elementary-heat.png

事前準備
========

GISソフトウエア
---------------

本資料での説明で利用している GIS は、オープンソースの地理情報システムである「QGIS」を利用して進めます

- `QGIS 公式サイト <https://qgis.org/ja/site/>`_

なお、ソフトウエアのインストール方法や使い方は、国土交通省が公開されてるマニュアルなどを参考にしてみてください

- `QGIS 操作マニュアル - GISホームページ - 国土交通省 <http://nlftp.mlit.go.jp/ksj/other/manual.pdf>`_

ヒートマップで表現したい数値データ
----------------------------------

CSV形式で一行あたり ``小学校区名,数値`` の書式で、小学校区分の行数でCSVファイルを用意してください

- `サンプルCSVデータ <https://raw.githubusercontent.com/kon104/tzuka/master/qgis/takarazuka-area-elementary.csv>`_

