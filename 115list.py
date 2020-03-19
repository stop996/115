# -*- coding: UTF-8 -*-
#£¡/usr/bin/env pythonimport sys
import sqlite3
import warnings
warnings.filterwarnings("ignore")
import requests
import jsonpath
import json 
import pandas as pd
import re
import os
from sqlalchemy import create_engine

def pclist(UID,CID,SEID,offset='0'):
    url = "https://webapi.115.com/files?aid=1&cid=0&o=user_ptime&asc=0&show_dir=1&limit=11150&code=&scid=&snap=0&natsort=1&record_open_time=1&source=&format=json&type=4&star=&suffix="

    payload = {'offset':offset }

    cookies = dict(UID=UID,CID=CID,SEID=SEID)
    r = requests.get(url, params=payload, cookies=cookies)

    req = r.text

    jsonobj = json.loads(req) 


    return jsonobj['data']
def pccount(UID,CID,SEID):
    url = "https://webapi.115.com/files?aid=1&cid=0&o=user_ptime&asc=0&show_dir=1&limit=30&code=&scid=&snap=0&natsort=1&record_open_time=1&source=&format=json&type=4&star=&suffix="

    payload = {'offset':'0' }

    cookies = dict(UID=UID,CID=CID,SEID=SEID)
    r = requests.get(url, params=payload, cookies=cookies)

    req = r.text

    jsonobj = json.loads(req) 


    return jsonobj['count']

fname = 'cookie.txt'
with open(fname, 'r', encoding='utf-8') as f:  # 打开文件
    lines = f.readlines()  # 读取所有行
    cookie = lines[-1]  # 取最后一行
    if cookie == '': cookie = lines[-2]
cookie = cookie.strip(string.punctuation)
cookie = dict(item.split("=") for item in cookie.split(";"))
UID = cookie['UID']
CID = cookie['CID']
SEID = cookie['SEID']

pccount = pccount(UID,CID,SEID)

data = []
for i in range(round(pccount/1150+1)):
    offset = i*1150    
    data = data+pclist(UID,CID,SEID,offset)
df=pd.DataFrame(data)
df=df[['n','pc','ico','vdi','s','play_long','sha']]
df = df.drop_duplicates(subset='n')
#df.to_csv('C:/Result.csv',encoding = 'utf-8')
#df = df.dropna(subset=["vdi"])

df= df[df['n'].str.len() >7]


#会在/new/115/建立媒体文件夹
df['n'].apply(lambda x: open('/new/115/'+x,"w+").close())

#建立115数据库
engine= create_engine('sqlite:///115.db')
df.to_sql('list', engine, if_exists='replace', index=False)
