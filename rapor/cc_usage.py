import json,sys,collections,datetime
def parse(path):
    seen={}; models=collections.Counter(); tools=collections.Counter(); users=0; first=last=None; sidechain=0
    for line in open(path,encoding='utf-8',errors='replace'):
        try: e=json.loads(line)
        except: continue
        t=e.get('timestamp')
        if t:
            first=first or t; last=t
        if e.get('type')=='user' and not e.get('isMeta'):
            c=e.get('message',{}).get('content')
            if isinstance(c,str) or (isinstance(c,list) and any(x.get('type')=='text' for x in c if isinstance(x,dict))):
                users+=1
        if e.get('type')=='assistant':
            m=e.get('message',{}); mid=m.get('id') or e.get('uuid')
            u=m.get('usage') or {}
            if mid not in seen: 
                models[m.get('model')]+=1
                for x in m.get('content') or []:
                    if isinstance(x,dict) and x.get('type')=='tool_use': tools[x.get('name')]+=1
            else:
                for x in m.get('content') or []:
                    if isinstance(x,dict) and x.get('type')=='tool_use': tools[x.get('name')]+=1
            seen[mid]=u
    tot=collections.Counter()
    for u in seen.values():
        for k in ('input_tokens','cache_creation_input_tokens','cache_read_input_tokens','output_tokens'):
            tot[k]+=u.get(k) or 0
    return dict(path=path.split('/')[-1][:12],first=first,last=last,api_calls=len(seen),user_msgs=users,models=dict(models),tools=sum(tools.values()),top_tools=tools.most_common(6),**tot)
for p in sys.argv[1:]:
    print(json.dumps(parse(p),ensure_ascii=False))
