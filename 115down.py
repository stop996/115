#!coding=utf-8

from flask import Flask
from flask import request
import sys
import requests
import os
import sys
from urllib.parse import unquote


app = Flask(__name__)

@app.route("/info",methods=['GET',])
def sendinfo():
    url = request.args.get('url')
    url = unquote(url)
    name = request.args.get('name')
    name = unquote(name)
    size = int(request.args.get('size'))
    headers={}
    headers['User-Agent']='Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/62.0.3202.94 Safari/537.36 115Browser/9.2.1'
    f = open(name, "wb")
    f.truncate(size)
    f = open(name+'.tmp', "wb")
    f.truncate(0)
    f.close()
    start = size - 10485760
    end = size
    headers['Range'] = 'bytes={}-{}'.format(start, end)
    r = requests.get(url, headers=headers, stream=True)
    # 写入文件对应位置
    print(start)
    if r.status_code <300 :
        with open(name, "rb+") as f:
            f.seek(start)
            for chunk in r.iter_content(chunk_size=31457280):
                if chunk:
                    f.write(chunk)
    else:raise IOError('连接失败')
    headers['Range'] = 'bytes={}-{}'.format(0, start-1)
    r = requests.get(url, headers=headers, stream=True)
    # 写入文件对应位置

    if r.status_code <300 :
        print('ov')
        with open(name, "rb+") as f:
            f.seek(0)
            for chunk in r.iter_content(chunk_size=1048576):
                if chunk:
                    f.write(chunk)
            f.close()
            print('over')
            os.remove(name+'.tmp',)
            pass
    else:raise IOError('连接失败')

    return "over"


if __name__ == "__main__":
    app.run(host='0.0.0.0',port =7600)
